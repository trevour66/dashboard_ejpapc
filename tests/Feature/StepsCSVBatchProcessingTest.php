<?php

namespace Tests\Feature;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use App\CSVFileProcessor\CSVFileProcessor;


class StepsCSVBatchProcessingTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function process_steps_csv_route()
    {
        $processor = new CSVFileProcessor;

        $processor->process_steps_CSV();
    }

}
