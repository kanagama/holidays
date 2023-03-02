<?php

namespace Tests\Unit\Kanagama\Holidays;

use Kanagama\Calendarar\Holidays;
use PHPUnit\Framework\TestCase;

final class CalendarTest extends TestCase
{
    /**
     * @var Calendarar
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
}
