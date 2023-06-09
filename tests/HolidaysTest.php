<?php

namespace Tests\Unit\Kanagama\Holidays;

use Kanagama\Holidays\Holidays;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
final class HolidaysTest extends TestCase
{
    /**
     * @var Holidays
     */
    private Holidays $holidays;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->holidays = new Holidays();
    }

    /**
     * @test
     */
    public function checkPublicHolidayが呼び出せること()
    {
        $objectResponse = $this->holidays->checkPublicHoliday(2023, 3, 21);
        $this->assertTrue($objectResponse);
    }

    /**
     * @test
     */
    public function checkPublicHolidayが静的に呼び出せること()
    {
        $staticResponse = Holidays::checkPublicHoliday(2023, 3, 21);
        $this->assertTrue($staticResponse);
    }

    /**
     * @test
     */
    public function checkHolidayで祝日でない場合はfalseが返却されること()
    {
        $objectResponse = $this->holidays->checkPublicHoliday(2023, 3, 22);
        $this->assertFalse($objectResponse);
    }

    /**
     * @test
     */
    public function getPublicHolidayNameが呼び出せること()
    {
        $objectResponse = $this->holidays->getPublicHolidayName(2023, 3, 21);
        $this->assertNotEmpty($objectResponse);
    }

    /**
     * @test
     */
    public function getPublicHolidayNameが静的に呼び出せること()
    {
        $staticResponse = Holidays::getPublicHolidayName(2023, 3, 21);
        $this->assertNotEmpty($staticResponse);
    }

    /**
     * @test
     */
    public function getPublicHolidayNameで祝日でない場合はnullが返却されること()
    {
        $objectResponse = $this->holidays->getPublicHolidayName(2023, 3, 20);
        $this->assertNull($objectResponse);
    }

    /**
     * @test
     */
    public function checkDayBeforePublicHolidayが呼び出せること()
    {
        $objectResponse = $this->holidays->checkDayBeforePublicHoliday(2023, 3, 20);
        $this->assertTrue($objectResponse);
    }

    /**
     * @test
     */
    public function checkDayBeforePublicHolidayが静的に呼び出せること()
    {
        $staticResponse = Holidays::checkDayBeforePublicHoliday(2023, 3, 20);
        $this->assertTrue($staticResponse);
    }

    /**
     * @test
     */
    public function checkDayBeforePublicHolidayで祝前日でない場合はfalseが返却されること()
    {
        $objectResponse = $this->holidays->checkDayBeforePublicHoliday(2023, 3, 21);
        $this->assertFalse($objectResponse);
    }

    /**
     * @test
     */
    public function checkDayAfterPublicHolidayが呼び出せること()
    {
        $objectResponse = $this->holidays->checkDayAfterPublicHoliday(2023, 3, 22);
        $this->assertTrue($objectResponse);
    }

    /**
     * @test
     */
    public function checkDayAfterPublicHolidayが静的に呼び出せること()
    {
        $staticResponse = Holidays::checkDayAfterPublicHoliday(2023, 3, 22);
        $this->assertTrue($staticResponse);
    }

    /**
     * @test
     */
    public function checkDayAfterPublicHolidayで祝後日でない場合はfalseが返却されること()
    {
        $objectResponse = $this->holidays->checkDayAfterPublicHoliday(2023, 3, 21);
        $this->assertFalse($objectResponse);
    }

    /**
     * @test
     */
    public function checkHolidayが呼び出せること()
    {
        $objectResponse = $this->holidays->checkHoliday(2023, 3, 19);
        $this->assertTrue($objectResponse);
    }

    /**
     * @test
     */
    public function checkHolidayが静的に呼び出せること()
    {
        $staticResponse = Holidays::checkHoliday(2023, 3, 19);
        $this->assertTrue($staticResponse);
    }

    /**
     * @test
     * @dataProvider checkHolidaysProvider
     *
     * @param  bool  $result
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     */
    public function checkHolidayで正しく休日判定がされること(
        bool $result,
        int $year,
        int $month,
        int $day
    ) {
        $this->assertEquals($result, $this->holidays->checkHoliday($year, $month, $day));
    }

    /**
     * @return array
     */
    public function checkHolidaysProvider(): array
    {
        return [
            '土曜日' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 18,
            ],
            '日曜日' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 19,
            ],
            '平日月曜日' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 20,
            ],
            '祝日' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 21,
            ],
            '平日水曜日' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 22,
            ],
        ];
    }

    /**
     * @test
     */
    public function addPublicHolidayが呼び出せること()
    {
        $this->holidays->addPublicHoliday(2023, 3, 20, '個人的祝日');
        $objectResponse = $this->holidays->getPublicHolidayName(2023, 3, 20);
        $this->assertEquals($objectResponse, '個人的祝日');
    }

    /**
     * @test
     */
    public function addPublicHolidayが静的に呼び出せること()
    {
        // 静的呼び出しができること
        Holidays::addPublicHoliday(2023, 3, 20, '個人的祝日');
        $staticResponse = Holidays::getPublicHolidayName(2023, 3, 20);
        $this->assertEquals($staticResponse, '個人的祝日');
    }

    /**
     * 週末判定が正常に行われる
     *
     * @test
     * @dataProvider checkWeekEndProvider
     *
     * @param  bool  $result
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     */
    public function checkWeekEndの判定が正常に行われること(
        bool $result,
        int $year,
        int $month,
        int $day
    ) {
        $holidays = new Holidays();

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('checkWeekEnd');
        $method->setAccessible(true);

        $this->assertEquals(
            $result,
            $method->invoke($holidays, $year, $month, $day)
        );
    }

    /**
     * @return array
     */
    public function checkWeekEndProvider(): array
    {
        return [
            '土曜日はtrue' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 11,
            ],
            '日曜日はtrue' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 12,
            ],
            '月曜日はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 13,
            ],
        ];
    }

    /**
     * 曜日番号が正常に返却される
     *
     * @test
     */
    public function getWeekNoが正常に呼び出せること()
    {
        $holidays = new Holidays();

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('getWeekNo');
        $method->setAccessible(true);

        $this->assertSame(
            1,
            $method->invoke($holidays, 2023, 3, 13)
        );
    }

    /**
     * @test
     * @dataProvider checkSundayProvider
     *
     * @param  bool  $result
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     */
    public function checkSundayの判定が正常に行われること(
        bool $result,
        int $year,
        int $month,
        int $day
    ) {
        $holidays = new Holidays();

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('checkSunday');
        $method->setAccessible(true);

        $this->assertEquals(
            $result,
            $method->invoke($holidays, $year, $month, $day)
        );
    }

    /**
     * @return array
     */
    public function checkSundayProvider(): array
    {
        return [
            '土曜日はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 11,
            ],
            '日曜日はtrue' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 12,
            ],
            '月曜日はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 13,
            ],
        ];
    }

    /**
     * @test
     */
    public function checkRealPublicHolidayが正常に呼び出せること()
    {
        $holidays = new Holidays();

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('checkRealPublicHoliday');
        $method->setAccessible(true);

        $this->assertEquals(
            true,
            $method->invoke($holidays, 2023, 3, 21)
        );
    }

    /**
     * @test
     */
    public function checkRealPublicHolidaysでオレオレ祝日はfalseになること()
    {
        $holidays = new Holidays();

        $holidays->addPublicHoliday(2023, 3, 20, 'オレオレ祝日');

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('checkRealPublicHoliday');
        $method->setAccessible(true);

        $this->assertEquals(
            false,
            $method->invoke($holidays, 2023, 3, 20)
        );
    }

    /**
     * 祝日に挟まれた平日の場合の振替休日判定
     *
     * @test
     * @dataProvider checkFurikaeHolidaySandwichProvider
     *
     * @param  bool  $result
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     */
    public function checkFurikaeHolidaySandwichの判定が正常に行われる(
        bool $result,
        int $year,
        int $month,
        int $day
    ) {
        $holidays = new Holidays(__DIR__ . '/files/test.csv');

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('checkFurikaeHolidaySandwich');
        $method->setAccessible(true);

        $this->assertEquals(
            $result,
            $method->invoke($holidays, $year, $month, $day)
        );
    }

    /**
     * @return array
     */
    public function checkFurikaeHolidaySandwichProvider(): array
    {
        return [
            '祝日に挟まれている平日の場合はtrue' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 2,
            ],
            '祝日に挟まれている祝日の場合はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 13,
            ],
            '祝日に挟まれている土曜の場合はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 25,
            ],
            '祝日に挟まれている日曜の場合はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 19,
            ],
        ];
    }

    /**
     * 日曜日が祝日の場合の振替休日判定
     *
     * @test
     * @dataProvider checkFurikaeHolidaySundayProvider
     *
     * @param  bool  $result
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     */
    public function checkFurikaeHolidaySundayの判定が正常に行われる(bool $result, int $year, int $month, int $day)
    {
        $holidays = new Holidays(__DIR__ . '/files/test.csv');

        $reflection = new ReflectionClass($holidays);
        $method = $reflection->getMethod('checkFurikaeHolidaySunday');
        $method->setAccessible(true);

        $this->assertEquals(
            $result,
            $method->invoke($holidays, $year, $month, $day)
        );
    }

    /**
     * @return array
     */
    public function checkFurikaeHolidaySundayProvider(): array
    {
        return [
            '前日が日曜祝日の平日はtrue' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 6,
            ],
            '日曜祝日と祝日が続く場合の最初の平日はtrue' => [
                'result' => true,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 15,
            ],
            '前日が月曜祝日の平日はfalse' => [
                'result' => false,
                'year'   => 2023,
                'month'  => 3,
                'day'    => 21,
            ],
        ];
    }

    /**
     * @test
     */
    public function getPublicHolidayNameで振替休日の場合()
    {
        $holidays = new Holidays(__DIR__ . '/files/test.csv');
        $this->assertEquals('振替休日', $holidays->getPublicHolidayName(2023, 3, 6));
    }
}
