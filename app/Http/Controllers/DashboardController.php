<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Calculators\LeadsCalculator;
use App\Calculators\FinancesCalculator;
use App\Calculators\CaseCalculator;
use App\Calculators\Timeframe;
use DateTime;

class DashboardController extends Controller
{
    //

    public function index()
    {
        $defaultTimeframe = ALL;

        $leadCalculator = new LeadsCalculator();

        $leadCalculator->setTimeframe($defaultTimeframe);

        $leadCalculator->loadAllLeads();
        $leadCalculator->prepareNewLeads();

        $leadCalculator->loadRetainedLeadsFromLeadsCreatedWithinTimespan();        
        $leadCalculator->prepareRetainedLeads();

        return Inertia::render('Dashboard', [
            "leads" => [
                "new_leads" => $leadCalculator->newLeadsData,
                "retained_leads" => $leadCalculator->retainedLeadsData,
            ],
            "timeframes" => Timeframe::getAllTimeframes()
        ]);
    }

    public function leadsByStepAndSource(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'dataType' => 'required|string|in:count,leadData',
            ]);

            $leadCalculator = new LeadsCalculator();

            $leadCalculator->loadLeadsWhereDateCreatedIsWithinTimespan($validated['from'], $validated['to']);

            $step_res = [];
            $source_res = [];

            if ($validated['dataType'] == 'count') {
                $step_res = $leadCalculator->prepareLeadByCurrentStep_count($validated['from'], $validated['to']);
                $source_res = $leadCalculator->prepareLeadBySource_count($validated['from'], $validated['to']);
            }

            $resData = response(json_encode(
                [
                    "data" => [
                        "step" => $step_res,
                        "source" => $source_res
                    ],
                    "message" => "success"
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger($th->getMessage());

            $resData = response(json_encode(
                [
                    'message' => "error"
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }

        //code...
    }

    public function leadsAverages(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'dataType' => 'required|string|in:count,leadData',
            ]);

            $leadCalculator = new LeadsCalculator();
            $leadCalculator->loadRetainedLeadsFromLeadsCreatedWithinTimespan($validated['from'], $validated['to']);
            $leadCalculator->loadLeadStatusChangeLogWithCurrentStatusAsIntakeSheduled($validated['from'], $validated['to']);
            $leadCalculator->loadLeadStatusChangeLogWithCurrentStatusAsConsultationScheduled($validated['from'], $validated['to']);
            $leadCalculator->loadConsultationChangeLogWithCCL_OutcomeAsRetainerMeetingScheduled($validated['from'], $validated['to']);
            $leadCalculator->loadOpenLeads($validated['from'], $validated['to']);

            if ($validated['dataType'] == 'count') {
                $leadCalculator->prepareAverageTime_newLead_to_retained();
                $leadCalculator->prepareAverageTime_newLead_to_intake_on_leadStatus();
                $leadCalculator->prepareAverageTime_newLead_to_consultation_scheduled_on_leadStatus();
                $leadCalculator->prepareAverageTime_newLead_to_retainer_meeting_on_consultation_change_log();
                $leadCalculator->prepareAverageTime_Lead_is_open();
            }

            $resData = response(json_encode(
                [
                    "data" => [
                        "averages" => [
                            "newLead_to_retained" => $leadCalculator->leads_averageDaysToRetained,
                            "newLead_to_intake_on_leadStatus" => $leadCalculator->leads_averageDaysToIntakeSchedule,
                            "newLead_to_consultation_scheduled_on_leadStatus" => $leadCalculator->leads_averageDaysToConsultationSchedule,
                            "newLead_to_retainer_meeting_on_consultation_change_log" => $leadCalculator->leads_averageDaysToRetainerMeeting,
                            "lead_is_open" => $leadCalculator->leads_averageDaysLeadIsOpen,
                        ]
                    ],
                    "message" => "success"
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger($th->getMessage());

            $resData = response(json_encode(
                [
                    'message' => "error"
                ]
            ), 400)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function matterFinanceCount(Request $request)
    {
        try {

            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'dataType' => 'required|string|in:count,leadData',
            ]);

            $matterFinancesCalculator = new FinancesCalculator();
            $matterFinancesCalculator->loadAnticipatedFunds($validated['from'], $validated['to']);
            $matterFinancesCalculator->loadAnticipatedFundsThatHasBeenReceived($validated['from'], $validated['to']);


            $matterFinancesCalculator->calculateAllFinancies_fund_received();
            $matterFinancesCalculator->calculateAllFinancies();


            $resData = response(json_encode(
                [
                    "data" => [
                        "financial_data" => [
                            "anticipate_funds" => [
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                                "data" => [
                                    "count" => $matterFinancesCalculator->total_anticipatedFunds
                                ]
                            ],
                            "atty_fees_collected" => [
                                "data" => [
                                    "count" => $matterFinancesCalculator->total_attyFeescollected
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ],
                            "average_atty_fees_collected" => [
                                "data" => [
                                    "count" => $matterFinancesCalculator->average_attyFeescollected
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ],
                            "atty_fees_by_responsible_atty" => [
                                "data" => [
                                    "count" => $matterFinancesCalculator->average_attyFeescollectedByResponsibeAtty
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ],

                        ],
                        "date_used" => [
                            "to" => $validated['to'],
                            "from" => $validated['from'],
                        ]
                    ],
                    "message" => "success",
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger($th->getMessage());

            $resData = response(json_encode(
                [
                    "data" => null,
                    'message' => "error"
                ]
            ), 400)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function generalCasesData(Request $request)
    {
        try {

            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'dataType' => 'string|in:count,leadData',
            ]);

            $matterCasesCalculator = new CaseCalculator();
            $matterCasesCalculator->loadCases($validated['from'], $validated['to']);
            $matterCasesCalculator->loadOpenCases_query($validated['from'], $validated['to']);

            $resData = response(json_encode(
                [
                    "data" => [
                        "cases_data" => [
                            "open_case" => [
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                                "data" => [
                                    "count" => $matterCasesCalculator->getNumberOfOpenCases()
                                ]
                            ],
                            "cases_by_current_step" => [
                                "data" => [
                                    "count" => $matterCasesCalculator->prepareCasesByCurrentStep_count()
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ],
                            "cases_by_atty" => [
                                "data" => [
                                    "count" => $matterCasesCalculator->prepareCasesByResponsibileAtty_count()
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ],
                            "cases_accessed_within_more_than_ten_day_ago" => [
                                "data" => [
                                    "count" => $matterCasesCalculator->prepareCasesWhereLastAccessedDateIsGreaterThan10Days_count()
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ],
                            "cases_with_next_step_overdue" => [
                                "data" => [
                                    "count" => $matterCasesCalculator->prepareCasesWhereNextStepIsOverDue_count()
                                ],
                                "date_used" => [
                                    "to" => $validated['to'],
                                    "from" => $validated['from'],
                                ],
                            ]

                        ],
                        "date_used" => [
                            "to" => $validated['to'],
                            "from" => $validated['from'],
                        ]
                    ],
                    "message" => "success",
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger($th->getMessage());

            $resData = response(json_encode(
                [
                    "data" => null,
                    'message' => "error"
                ]
            ), 400)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }
}
