<?php

namespace Kanagama\Holidays;

use BadMethodCallException;
use Carbon\Carbon;

/**
 * 日本の祝日を管理する
 *
 * @method bool checkPublicHoliday(int $year, int $month, int $day)
 * @method string|null getPublicHolidayName(int $year, int $month, int $day)
 * @method bool checkBeforePublicHoliday(int $year, int $month, int $day)
 * @method bool checkAfterHoliday(int $year, int $month, int $day)
 * @method bool checkHoliday(int $year, int $month, int $day)
 * @method bool addPublicHoliday(int $year, int $month, int $day, string $holidayName)
 *
 * @method static bool checkPublicHoliday(int $year, int $month, int $day)
 * @method static string|null getPublicHolidayName(int $year, int $month, int $day)
 * @method static bool checkBeforePublicHoliday(int $year, int $month, int $day)
 * @method static bool checkAfterPublicHoliday(int $year, int $month, int $day)
 * @method static bool checkHoliday(int $year, int $month, int $day)
 * @method static bool addPublicHoliday(int $year, int $month, int $day, string $holidayName)
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
final class Holidays
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array
     */
    private array $holidays = [];

    /**
     *
     */
    public function __construct()
    {
        $handle = fopen(__DIR__ . '/../../csv/syukujitsu.csv', 'r');

        // 1行目はタイトルなので除去
        $titles = fgetcsv($handle);
        while (($line = fgetcsv($handle)) !== false) {
            list($year, $month, $day) = explode("/", $line[0]);
            $holidayName = mb_convert_encoding($line[1], 'UTF-8', 'Shift_JIS');

            $this->holidays[(int) $year][(int) $month][(int) $day] = $holidayName;
        }

        fclose($handle);
    }

    /**
     * @return Holidays
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Holidays();
        }
        return self::$instance;
    }

    /**
     * 動的呼び出し
     *
     * @param  string  $name
     * @param  array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        $callMethod = '_' . $name;
        if (method_exists($this, $callMethod)) {
            return call_user_func_array(array($this, $callMethod), $args);
        }

        throw new BadMethodCallException;
    }

    /**
     * 静的呼び出し
     *
     * @param  string  $name
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        $instance = self::getInstance();

        $callMethod = '_' . $name;
        if (method_exists($instance, $callMethod)) {
            return call_user_func_array(array($instance, $callMethod), $args);
        }

        throw new BadMethodCallException;
    }

    /**
     * 祝日チェック
     *
     * @return bool
     */
    private function _checkPublicHoliday(int $year, int $month, int $day): bool
    {
        return !empty($this->holidays[$year][$month][$day]);
    }

    /**
     * 祝日名を取得する
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return string|null
     */
    private function _getPublicHolidayName(int $year, int $month, int $day): ?string
    {
        if ($this->_checkPublicHoliday($year, $month, $day)) {
            return $this->holidays[$year][$month][$day];
        }

        return null;
    }

    /**
     * 休日チェック
     * 土日もしくは祝日である
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return boolean
     */
    public function _checkHoliday(int $year, int $month, int $day): bool
    {
        return (
            in_array(
                (int) date('w', mktime(0, 0, 0, $month, $day, $year)),
                [
                    Carbon::SUNDAY,
                    Carbon::SATURDAY,
                ],
                true
            )
            ||
            $this->_checkPublicHoliday($year, $month, $day)
        );
    }

    /**
     * 祝前日かチェックする
     *
     * @return bool
     */
    private function _checkDayBeforePublicHoliday(int $year, int $month, int $day): bool
    {
        $carbon = (new Carbon($year . '/' . $month . '/' . $day))->addDay();

        return $this->_checkPublicHoliday($carbon->year, $carbon->month, $carbon->day);
    }

    /**
     * 祝後日かチェックする
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return bool
     */
    private function _checkDayAfterPublicHoliday(int $year, int $month, int $day): bool
    {
        $carbon = (new Carbon($year . '/' . $month . '/' . $day))->subDay();

        return $this->_checkPublicHoliday($carbon->year, $carbon->month, $carbon->day);
    }

    /**
     * 祝日を追加
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @param  string  $holidayName
     * @return void
     */
    private function _addPublicHoliday(int $year, int $month, int $day, string $holidayName)
    {
        $this->holidays[$year][$month][$day] = $holidayName;
    }
}
