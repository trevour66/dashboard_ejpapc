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

class processIncomingStepsCSVTest extends TestCase
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
        $file = fopen(storage_path('matters_CSVs/test_file_steps.csv'), "r") or die("Unable to open file!");

        $fileContent = fread($file, filesize(storage_path('/matters_CSVs/test_file_steps.csv')));

        fclose($file);

        $file = UploadedFile::fake()->createWithContent('test_file_steps.csv', $fileContent);

        $response = $this->postJson('/process-step-csv', [
            'auth' => 'testing',
            'file' => $file
        ]);

        $response->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json->has('file_name')->etc());

        $processedFilename = $response->collect('file_name');

        $processedFilename = print_r($processedFilename->first(), true);

        Storage::disk()->assertExists("steps_CSVs/" . $processedFilename);

        // $this->confirmDatabaseHasCSVData($processedFilename);
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
            $this->assertIndependentTables('step_name', $record['Current Step'], 'a_s__steps');

            $createdOrUpdatedMatter = matter::where('matter_actionstep_id', '=', $record['ID'])->first();

            $createdOrUpdatedAS_Step =  AS_Step::where('step_name', '=', $record['Current Step'])->first();

            $this->assertInstanceOf(AS_Step::class, $createdOrUpdatedMatter->currentStep);
            $this->assertEquals($createdOrUpdatedMatter->currentStep->step_name, $createdOrUpdatedAS_Step->step_name);
        }
    }
}
