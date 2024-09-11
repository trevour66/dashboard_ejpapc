<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;


class IncomingCSVFileUploadTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function process_incoming_matters_csv_route_is_accessible()
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

        Storage::disk()->assertExists("pending_matters_CSVs/" . $processedFilename);

    }

}
