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

class processIncomingCSVTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function process_incoming_csv_route_is_accessible()
    {
        $file = fopen(storage_path('matters_CSVs/test_file.csv'), "r") or die("Unable to open file!");

        $fileContent = fread($file, filesize(storage_path('/matters_CSVs/test_file.csv')));

        fclose($file);

        $file = UploadedFile::fake()->createWithContent('test_file.csv', $fileContent);

        $response = $this->postJson('/process-csv', [
            'auth' => 'testing',
            'file' => $file
        ]);

        $response->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json->has('file_name')->etc());

        $processedFilename = $response->collect('file_name');

        $processedFilename = print_r($processedFilename->first(), true);

        Storage::disk()->assertExists("matters_CSVs/" . $processedFilename);

        $this->confirmDatabaseHasCSVData($processedFilename);
    }

    private function assertIndependentTables($dataFieldName, $dataValue, $tableName)
    {
        if ($dataValue ?? false) {
            $data = array();

            $data[$dataFieldName] = $dataValue;
            $this->assertDatabaseHas($tableName, $data);
        }
    }

    private function confirmDatabaseHasCSVData($csv_temp_name)
    {
        $path = storage_path('app/matters_CSVs') . '/' . $csv_temp_name;

        // Process CSV
        $csv = Reader::createFromStream(fopen($path, 'r+'));
        $csv->setHeaderOffset(0); //set the CSV header offset

        $stmt = Statement::create();
        $records = $stmt->process($csv);

        foreach ($records as $record) {
            $this->assertIndependentTables('MT_type', $record['Matter Type'], 'matter_types');
            $this->assertIndependentTables('step_name', $record['Current Step'], 'a_s__steps');
            $this->assertIndependentTables('MSt_status', $record['Matter Status'], 'matter_statuses');
            $this->assertIndependentTables('ASALA_name', $record['Assigned To'], 'action_step_attorneys_legal_assistants');

            $this->assertIndependentTables('LSt_status', $record['Lead Status'], 'lead_statuses');
            $this->assertIndependentTables('LOS_status', $record['Lead Overall Status'], 'lead_overall_statuses');
            $this->assertIndependentTables('LS_source', $record['Lead Source'], 'lead_sources');
            $this->assertIndependentTables('IS_status', $record['Intake Status'], 'intake_statuses');

            $this->assertIndependentTables('ID_deposition', $record['Intake Disposition'], 'intake_depositions');
            $this->assertIndependentTables('ISP_platform', $record['Intake Scheduled By'], 'intake_schedule_platforms');
            $this->assertIndependentTables('CSP_platform', $record['Consultation Scheduled By'], 'consultation_schedule_platforms');

            $this->assertIndependentTables('matter_actionstep_id', $record['ID'], 'matters');

            $createdOrUpdatedMatter = matter::where('matter_actionstep_id', '=', $record['ID'])->first();

            $createdOrUpdatedMatterType = matterType::where('MT_type', '=', $record['Matter Type'])->first();
            $createdOrUpdatedAS_Step =  AS_Step::where('step_name', '=', $record['Current Step'])->first();
            $createdOrUpdatedMatterStatus =  matterStatus::where('MSt_status', '=', $record['Matter Status'])->first();
            $createdOrUpdatedMatterAttorrney = actionStep_attorneys_legalAssistant::where('ASALA_name', '=', $record['Assigned To'])->first();


            // logger(print_r($createdOrUpdatedMatter->lead, true));
            // logger(print_r(lead::class, true));

            $this->assertInstanceOf(matterType::class, $createdOrUpdatedMatter->currentMatterType);
            $this->assertEquals($createdOrUpdatedMatter->currentMatterType->MT_type, $createdOrUpdatedMatterType->MT_type);

            $this->assertInstanceOf(AS_Step::class, $createdOrUpdatedMatter->currentStep);
            $this->assertEquals($createdOrUpdatedMatter->currentStep->step_name, $createdOrUpdatedAS_Step->step_name);

            $this->assertInstanceOf(matterStatus::class, $createdOrUpdatedMatter->currentMatterStatus);
            $this->assertEquals($createdOrUpdatedMatter->currentMatterStatus->MSt_status, $createdOrUpdatedMatterStatus->MSt_status);

            $this->assertInstanceOf(actionStep_attorneys_legalAssistant::class, $createdOrUpdatedMatter->currentMatterAttorney);
            $this->assertEquals($createdOrUpdatedMatter->currentMatterAttorney->ASALA_name, $createdOrUpdatedMatterAttorrney->ASALA_name);

            if ($record['Primary Participant Email'] ?? false) {
                $createOrUpdateLead = lead::where('lead_email', '=', $record['Primary Participant Email'])->first() ?? false;

                $this->assertInstanceOf(lead::class, $createdOrUpdatedMatter->lead);
                $this->assertEquals($createdOrUpdatedMatter->lead->lead_email, $createOrUpdateLead->lead_email);
            }
        }
    }
}
