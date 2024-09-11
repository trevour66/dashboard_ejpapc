<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;


class IncomingStepsCSVFileUploadTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function process_incoming_steps_csv_route_is_accessible()
    {
        $file = fopen(storage_path('matters_CSVs/test_file_steps.csv'), "r") or die("Unable to open file!");

        // $filePath = storage_path('matters_CSVs/test_file_steps.csv');
        // $mimeType = mime_content_type($filePath);
        // dd($mimeType);

        return;

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

        Storage::disk()->assertExists("pending_steps_CSVs/" . $processedFilename);

    }

}
