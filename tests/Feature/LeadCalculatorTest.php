<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Calculators\LeadsCalculator;

// define('THIS_WEEK', 'this_week');
// define('THIS_MONTH', 'this_month');
// define('TWO_MONTH', 'two_month');
// define('THREE_MONTH', 'three_month');
// define('ALL', 'all');

class LeadCalculatorTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function prepareLeadByTimespan()
    {
        $leadsCalculator = new LeadsCalculator();
        // $leadsCalculator->prepareLeadByTimespan('all');
        $leadsCalculator->prepareLeadByTimespan('this_week');
        // $leadsCalculator->prepareLeadByTimespan('this_month');
        // $leadsCalculator->prepareLeadByTimespan('two_month');
        // $leadsCalculator->prepareLeadByTimespan('three_month');
        $this->assertTrue(true);
    }
}
