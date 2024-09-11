<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequirementsController;
use App\Http\Controllers\ActionStepCredRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CasesController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LeadsController;

use App\Http\Controllers\IncomingCSVRequestProcessor;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/process-csv', [IncomingCSVRequestProcessor::class, 'storeMattersForFutureProcessing'])->name('process_incoming_csv');
Route::post('/process-step-csv', [IncomingCSVRequestProcessor::class, 'storeStepsForFutureProcessing'])->name('process_steps_incoming_csv');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
});

Route::middleware('auth')->group(function () {
    // Route::get('/dashboard', function () {
    //     return Inertia::render('Dashboard');
    // })->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/get-leads-by-step-and-source', [DashboardController::class, 'leadsByStepAndSource'])->name('leadsByStepAndSource');
    Route::post('/get-leads-averages', [DashboardController::class, 'leadsAverages'])->name('leadsAverages');
    Route::post('/get-leads-by-step', [LeadsController::class, 'getLeadsByStep'])->name('leads.getLeadsByStep');
    Route::post('/get-leads-by-source', [LeadsController::class, 'getLeadsBySource'])->name('leads.getLeadsBySource');
    Route::post('/get-retained-leads', [LeadsController::class, 'getSelectedRetainedLeads'])->name('leads.getSelectedRetainedLeads');
    Route::post('/get-new-leads', [LeadsController::class, 'getSelectedNewLeads'])->name('leads.getSelectedNewLeads');
    

    Route::post('/get-finance-count', [DashboardController::class, 'matterFinanceCount'])->name('matterFinanceCount');
    Route::post('/get-fees-by-atty', [FinanceController::class, 'getFeesByResponsibleAtty'])->name('finance.getFeesByResponsibleAtty');
    Route::post('/get-anticipate-funds', [FinanceController::class, 'getAnticipatedFunds'])->name('finance.getAnticipatedFunds');
    Route::post('/get-atty-fees-collected', [FinanceController::class, 'getAttyFeesCollected'])->name('finance.getAttyFeesCollected');

    Route::post('/get-cases-startup-data', [DashboardController::class, 'generalCasesData'])->name('generalCasesData');
    Route::post('/get-open-cases', [CasesController::class, 'getOpenCases'])->name('cases.getOpenCases');
    Route::post('/get-cases-by-step', [CasesController::class, 'getCasesByStep'])->name('cases.getCasesByStep');
    Route::post(
        '/get-cases-by-atty',
        [CasesController::class, 'getCasesByAtty']
    )->name('cases.getCasesByAtty');
    Route::post('/get-cases-accessed-within-ten-days', [CasesController::class, 'getCaseAccessedWithin10Days'])->name('cases.getCaseAccessedWithin10Days');
    Route::post('/get-cases-next-step-overdue', [CasesController::class, 'getCasesWithNextStepOverDue'])->name('cases.getCasesWithNextStepOverDue');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
