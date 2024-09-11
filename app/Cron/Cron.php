<?php

namespace App\Cron;

use App\CSVFileProcessor\CSVFileProcessor;


$processor = new CSVFileProcessor;
$processor->process_matters_CSV();
$processor->process_steps_CSV();
