<?php

namespace Tests\Unit\Kanagama\Holidays;

use Kanagama\Holidays\Holidays;
use PHPUnit\Framework\TestCase;

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
     *
     * @dataProvider checkHolidaysProvider
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
                'year' => 2023,
                'month' => 3,
                'day' => 18,
            ],
            '日曜日' => [
                'result' => true,
                'year' => 2023,
                'month' => 3,
                'day' => 19,
            ],
            '平日月曜日' => [
                'result' => false,
                'year' => 2023,
                'month' => 3,
                'day' => 20,
            ],
            '祝日' => [
                'result' => true,
                'year' => 2023,
                'month' => 3,
                'day' => 21,
            ],
            '平日水曜日' => [
                'result' => false,
                'year' => 2023,
                'month' => 3,
                'day' => 22,
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
}
