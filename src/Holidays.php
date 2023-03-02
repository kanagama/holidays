<?php

namespace Kanagama\Calendarar;

use Carbon\Carbon;

/**
 * @method bool checkHoliday(int $year, int $month, int $day)
 * @method string|null getHolidayName(int $year, int $month, int $day)
 * @method bool checkBeforeHoliday(int $year, int $month, int $day)
 * @method bool checkAfterHoliday(int $year, int $month, int $day)
 *
 * @method static bool checkHoliday(int $year, int $month, int $day)
 * @method static string|null getHolidayName(int $year, int $month, int $day)
 * @method static bool checkBeforeHoliday(int $year, int $month, int $day)
 * @method static bool checkAfterHoliday(int $year, int $month, int $day)
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
final class Holidays
{
    /**
     * @var array
     */
    private array $holidays = [];

    /**
     *
     */
    public function __construct()
    {
        $handle = fopen("/var/www/html/vendor/kanagama/csv/syukujitsu.csv", 'r');

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
        $instance = new self();
        call_user_func_array(array($instance, 'reset'), []);

        $callMethod = '_' . $name;
        if (method_exists($instance, $callMethod)) {
            return call_user_func_array(array($instance, $callMethod), $args);
        }
    }

    /**
     * 祝日チェック
     *
     * @return bool
     */
    private function _checkHoliday(int $year, int $month, int $day): bool
    {
        return !empty($this->holidays[$year][$month][$day]);
    }

    /**
     * 祝日名を取得する
     *
     * @return string|null
     */
    private function _getHolidayName(int $year, int $month, int $day): ?string
    {
        if ($this->checkHoliday($year, $month, $day)) {
            return $this->holidays[$year][$month][$day];
        }

        return null;
    }

    /**
     * 祝前日かチェックする
     *
     * @return bool
     */
    private function _checkDayBeforeHoliday(int $year, int $month, int $day): bool
    {
        $carbon = (new Carbon($year . '/' . $month . '/' . $day))->addDay();

        return $this->checkHoliday($carbon->year, $carbon->month, $carbon->day);
    }

    /**
     * 祝後日かチェックする
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return bool
     */
    private function _checkDayAfterHoliday(int $year, int $month, int $day): bool
    {
        $carbon = (new Carbon($year . '/' . $month . '/' . $day))->subDay();

        return $this->checkHoliday($carbon->year, $carbon->month, $carbon->day);
    }
}
