<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Calculators\Timeframe;
use Tests\TestCase;

use DateTime;

class timeframeTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function process_to_and_from()
    {
        $timeframeHandler = new timeframe('three_month');

        $timeframeHandler->process_to_and_from();

        $to = $timeframeHandler->to;
        $from = $timeframeHandler->from;

        $this->assertInstanceOf(DateTime::class, $to);
        $this->assertInstanceOf(DateTime::class, $from);
    }
}
