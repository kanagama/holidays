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
     * @param  string|null  $path
     */
    public function __construct(string $path = null)
    {
        $csvPath = $path;

        if (empty($path)) {
            $csvPath = $this->findVendorDir() . '/kanagama/csv/syukujitsu.csv';
        }

        $handle = fopen($csvPath, 'r');

        // 1行目はタイトルなので除去
        fgetcsv($handle);
        while (($line = fgetcsv($handle)) !== false) {
            list($year, $month, $day) = explode("/", $line[0]);
            $holidayName = mb_convert_encoding($line[1], 'UTF-8', 'Shift_JIS');
            // 振替休日はロジックで判断させるため登録しない
            if (!$path && $holidayName === '休日') {
                continue;
            }

            $this->holidays[(int) $year][(int) $month][(int) $day] = [
                'name'           => $holidayName,
                'public_holiday' => true,
            ];
        }

        fclose($handle);
    }

    /**
     * @static
     * @return Holidays
     */
    private static function getInstance()
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
     * @throws BadMethodCallException
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
     * @static
     * @param  string  $name
     * @param  array  $args
     * @return mixed
     * @throws BadMethodCallException
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
        return (
            !empty($this->holidays[$year][$month][$day])
            ||
            $this->checkFurikaeHolidaySandwich($year, $month, $day)
            ||
            $this->checkFurikaeHolidaySunday($year, $month, $day)
        );
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
        if (!empty($this->holidays[$year][$month][$day])) {
            return $this->holidays[$year][$month][$day]['name'];
        }

        if (
            $this->checkFurikaeHolidaySandwich($year, $month, $day)
            ||
            $this->checkFurikaeHolidaySunday($year, $month, $day)
        ) {
            return '振替休日';
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
     * @return bool
     */
    public function _checkHoliday(
        int $year,
        int $month,
        int $day
    ): bool {
        return (
            // 週末かどうか
            $this->checkWeekEnd($year, $month, $day)
            ||
            // 祝日かどうか
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
     * リアル祝前日かチェックする
     *
     * @return bool
     */
    private function checkRealDayBeforePublicHoliday(int $year, int $month, int $day): bool
    {
        $carbon = (new Carbon($year . '/' . $month . '/' . $day))->addDay();

        return $this->checkRealPublicHoliday($carbon->year, $carbon->month, $carbon->day);
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
     * リアル祝後日かチェックする
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return bool
     */
    private function checkRealDayAfterPublicHoliday(int $year, int $month, int $day): bool
    {
        $carbon = (new Carbon($year . '/' . $month . '/' . $day))->subDay();

        return $this->checkRealPublicHoliday($carbon->year, $carbon->month, $carbon->day);
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
        $this->holidays[$year][$month][$day]['name'] = $holidayName;
    }

    /**
     * 週末判定
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return bool
     */
    private function checkWeekEnd(int $year, int $month, int $day): bool
    {
        return in_array(
            $this->getWeekNo($year, $month, $day),
            [
                Carbon::SUNDAY,
                Carbon::SATURDAY,
            ],
            true
        );
    }

    /**
     * 曜日番号を取得
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return int
     */
    private function getWeekNo(int $year, int $month, int $day): int
    {
        return (int) date('w', mktime(0, 0, 0, $month, $day, $year));
    }

    /**
     * 日曜日判定
     *
     * @return bool
     */
    private function checkSunday(int $year, int $month, int $day): bool
    {
        return  $this->getWeekNo($year, $month, $day) === Carbon::SUNDAY;
    }

    /**
     * リアル祝日チェック
     *
     * @return bool
     */
    private function checkRealPublicHoliday(int $year, int $month, int $day): bool
    {
        return !empty($this->holidays[$year][$month][$day]['public_holiday']);
    }

    /**
     * 祝日に挟まれた平日の振替休日判定
     *
     * @return bool
     */
    private function checkFurikaeHolidaySandwich(int $year, int $month, int $day): bool
    {
        return (
            !$this->checkRealPublicHoliday($year, $month, $day)
            &&
            !$this->checkWeekEnd($year, $month, $day)
            &&
            $this->checkRealDayBeforePublicHoliday($year, $month, $day)
            &&
            $this->checkRealDayAfterPublicHoliday($year, $month, $day)
        );
    }

    /**
     * 日曜日が祝日の振替休日判定
     *
     * @return bool
     */
    private function checkFurikaeHolidaySunday(int $year, int $month, int $day): bool
    {
        // 平日かつ前日が祝日の条件を満たさなければ終了
        if ($this->checkWeekEnd($year, $month, $day)) {
            return false;
        }

        $carbon = new Carbon($year . '/' . $month . '/' . $day);
        while (true) {
            $carbon->subDay();

            if (!$this->checkRealPublicHoliday($carbon->year, $carbon->month, $carbon->day)) {
                return false;
            }

            if ($this->checkSunday($carbon->year, $carbon->month, $carbon->day)) {
                return true;
            }
        }
    }

    /**
     * vendor ディレクトリを検索する
     * 本当は要らないメソッドだが自動テストのために仕方なく作成…
     *
     * @return string|null
     */
    private function findVendorDir(): ?string
    {
        $dir = __DIR__;
        while ($dir !== '/') {
            $vendor_dir = $dir . '/vendor';
            if (is_dir($vendor_dir)) {
                return $vendor_dir;
            }
            $dir = dirname($dir);
        }
        return null;
    }
}
