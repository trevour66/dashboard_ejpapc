<?php

namespace App\Calculators;

use DateTime;

class Timeframe
{
    public $to;
    public $from;
    public $timeframe;

    public function __construct($timeframe)
    {
        $this->timeframe = $timeframe;
    }

    static function getAllTimeframes()
    {
        return config('app.timeframes_available') ?? [];
    }

    public function process_to_and_from()
    {
        $now = new \DateTime('now');
        $this->to = new \DateTime('now');

        switch ($this->timeframe) {
            case config('app.timeframes.this_week'):
                $interval = new \DateInterval('P7D');
                $this->from = $now->sub($interval);
                break;

            case config('app.timeframes.this_month'):
                $currentMonth = $now->format('m');
                $firstDayOfTheMonth = date("y-$currentMonth-01");

                $this->from = new
                    DateTime($firstDayOfTheMonth);
                break;

            case config('app.timeframes.two_month') || config('app.timeframes.three_month'):
                $currentMonth = $now->format('m');

                $lastDayOfMonth = date("Y-$currentMonth-t");
                $this->to = new DateTime($lastDayOfMonth);
                $this->from = new DateTime($lastDayOfMonth);

                $interval = null;

                if ($this->timeframe === config('app.timeframes.two_month')) {
                    $interval = new \DateInterval('P2M');
                } else if ($this->timeframe === config('app.timeframes.three_month')) {
                    $interval = new \DateInterval('P3M');
                }

                $this->from = $this->from->sub($interval);

                break;

            default:
                # code...
                break;
        }
    }
}
