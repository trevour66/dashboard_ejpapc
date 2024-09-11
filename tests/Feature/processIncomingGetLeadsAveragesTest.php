<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use League\Csv\Reader;
use League\Csv\Statement;

use App\Models\matterType;
use App\Models\AS_Step;
use App\Models\matterStatus;
use App\Models\actionStep_attorneys_legalAssistant;
use App\Models\leadStatus;
use App\Models\leadOverallStatus;
use App\Models\leadSource;
use App\Models\intakeStatus;
use App\Models\intakeDeposition;
use App\Models\intakePlatforms;
use App\Models\consultationSchedulePlatform;
use App\Models\lead;
use App\Models\matter;

class processIncomingGetLeadsAveragesTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function process_incoming_get_leads_averages()
    {
        $date1 = new \DateTime();

        // Clone the first date to ensure we have a separate DateTime object
        $date2 = clone $date1;

        // Modify the second date to be 3 months apart from the first date
        $date2->modify('-6 months');

        // Format the dates as needed, e.g., 'Y-m-d'
        $date_to_Formatted = $date1->format('Y-m-d');
        $date_from_Formatted = $date2->format('Y-m-d');

        logger(print_r($date_to_Formatted, true));
        logger(print_r($date_from_Formatted, true));


        $response = $this->postJson('/get-leads-averages', [
            'to' => $date_to_Formatted,
            'from' => $date_from_Formatted,
            'dataType' => 'count',
        ]);

        $response->assertStatus(200);
    }
}
