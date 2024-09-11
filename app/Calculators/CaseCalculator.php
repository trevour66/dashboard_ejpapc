<?php

namespace App\Calculators;

use App\Calculators\Calculator;

use App\Models\matterType;
use App\Models\AS_Step;
use App\Models\matterStatus;
use App\Models\actionStep_attorneys_legalAssistant;
use App\Models\consultationChangeLog;
use App\Models\leadStatus;
use App\Models\leadOverallStatus;
use App\Models\leadSource;
use App\Models\intakeStatus;
use App\Models\intakeDeposition;
use App\Models\intakePlatforms;
use App\Models\consultationSchedulePlatform;
use App\Models\currentStepChangeLog;
use App\Models\intakeChangeLog;
use App\Models\lead;
use App\Models\leadOverallStatusChangeLog;
use App\Models\leadStatusChangeLog;
use App\Models\matter;
use App\Models\matterNameChangeLog;
use App\Models\matterStatusChangeLog;
use App\Models\matterTypeChangeLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Calculators\Timeframe;
use DateTime;
use Error;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class CaseCalculator extends Calculator
{
    private ?Collection $loadedCasesWithinTimeframe  = null;
    private $loadedOpenCases  = null;

    private $loadedCasesWithinTimeframe_query  = null;
    private $loadedOpenCases_query  = null;

    public $casesByResponsibeAtty = [];


    public function loadCases($from = null, $to = null)
    {
        try {
            //code...
            $this->loadedCasesWithinTimeframe_query = matter::leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->leftJoin('action_step_attorneys_legal_assistants', 'matters.matter_assigned_to', '=', 'action_step_attorneys_legal_assistants.ASALA_id')
                ->leftJoin('a_s__steps', 'matters.matter_current_step', '=', 'a_s__steps.step_id');


            if (($from ?? false)) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);
                    $this->loadedCasesWithinTimeframe_query = $this->loadedCasesWithinTimeframe_query->where('matters.matter_date_created', '>=', $fromDate);
                }
            }


            if (($to ?? false)) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);
                    $this->loadedCasesWithinTimeframe_query = $this->loadedCasesWithinTimeframe_query->where('matters.matter_date_created', '<=', $toDate);
                }
            }

            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;

            $this->loadedCasesWithinTimeframe = $loadedCasesWithinTimeframe_query->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->get();

            // logger(print_r("loadCases " . count($this->loadedCasesWithinTimeframe), true));
        } catch (Throwable $th) {

            logger(print_r("loadCases Error " . $th->getMessage(), true));
            $this->loadedCasesWithinTimeframe = collect([]);
        }
    }

    public function loadOpenCases_query($from = null, $to = null)
    {
        try {
            //code...
            $this->loadedOpenCases_query = Matter::select('matters.matter_id', 'matters.matter_date_created', 'current_step_change_logs.CSCL_id', 'current_step_change_logs.CSCL_action_step_matter_id', 'current_step_change_logs.CSCL_current_step')
                ->leftJoin('current_step_change_logs', 'matters.matter_id', '=', 'current_step_change_logs.CSCL_action_step_matter_id')
                ->leftJoin('action_step_attorneys_legal_assistants', 'matters.matter_assigned_to', '=', 'action_step_attorneys_legal_assistants.ASALA_id')
                ->leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->leftJoin('a_s__steps', 'current_step_change_logs.CSCL_current_step', '=', 'a_s__steps.step_id')
                ->whereNotIn('matters.matter_id', function (Builder $query) {
                    $query->select('matters.matter_id')
                        ->from('matters')
                        ->leftJoin('current_step_change_logs', 'matters.matter_id', '=', 'current_step_change_logs.CSCL_action_step_matter_id')
                        ->leftJoin('a_s__steps', 'current_step_change_logs.CSCL_current_step', '=', 'a_s__steps.step_id')
                        ->whereRaw('LOWER(a_s__steps.step_name) LIKE ?', ['%close%']);
                })
                ->whereIn('matters.matter_id', function (Builder $query) {
                    $query->selectRaw('DISTINCT CSCL_action_step_matter_id')
                        ->from('current_step_change_logs');
                });

            // dd($this->loadedOpenCases_query->toSql(), $this->loadedOpenCases_query->getBindings());

            // logger(print_r("Open cases: " . $this->loadedOpenCases_query, true));
            // logger(print_r("Open cases: " . count($this->loadedOpenCases_query), true));


            if (($from ?? false)) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);

                    $this->loadedOpenCases_query = $this->loadedOpenCases_query->where('matters.matter_date_created', '>=', $fromDate);
                }
            }


            if (($to ?? false)) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $this->loadedOpenCases_query = $this->loadedOpenCases_query->where('matters.matter_date_created', '<=', $toDate);
                }
            }

            $this->loadedOpenCases_query = $this->loadedOpenCases_query->orderBy('matters.matter_date_created', 'ASC');
        } catch (Throwable $th) {
            $this->loadedOpenCases = collect([]);

            logger(print_r("loadOpenLeads Error " . $th->getMessage(), true));
        }
    }

    public function getNumberOfOpenCases()
    {
        try {

            if (!($this->loadedOpenCases_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedOpenCases_query = clone $this->loadedOpenCases_query;

            $this->loadedOpenCases = $loadedOpenCases_query->get();

            return count($this->loadedOpenCases) ?? 0;
        } catch (Throwable $th) {
            logger("Error : getNumberOfOpenCases" . $th->getMessage());
            return "undefined";
        }
    }

    public function getOpenCases()
    {
        try {

            if (!($this->loadedOpenCases_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedOpenCases_query = clone $this->loadedOpenCases_query;


            $this->loadedOpenCases = $loadedOpenCases_query->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->cursorPaginate(20);

            return $this->loadedOpenCases;
        } catch (Throwable $th) {
            logger("Error : getOpenCases" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesByCurrentStep_count()
    {
        try {

            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;

            $casesByCurrentStep = $loadedCasesWithinTimeframe_query->groupBy('step_name')
                ->select(
                    [
                        'a_s__steps.step_name as step_name',
                        DB::raw('count(matters.matter_id) as matter_count'),
                    ]
                )
                ->get();

            // logger(print_r($casesByCurrentStep, true));
            // logger(print_r(count($casesByCurrentStep), true));


            return $casesByCurrentStep;
        } catch (Throwable $th) {
            logger("Error : prepareCasesByCurrentStep_count" . $th->getMessage());
            return [];
        }
    }

    public function getCasesByStep($stepname)
    {
        try {

            if (!($this->loadedCasesWithinTimeframe_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;

            $casesInGivenStep = $loadedCasesWithinTimeframe_query->where('a_s__steps.step_name', '=', $stepname)->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->cursorPaginate(20);

            return $casesInGivenStep;
        } catch (Throwable $th) {
            logger("Error : getCasesByStep" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesByResponsibileAtty_count()
    {

        try {
            $casesByResponsibleAtty = clone $this->loadedCasesWithinTimeframe;

            // logger(print_r($casesByResponsibleAtty, true));

            if (count($casesByResponsibleAtty ?? []) == 0) {
                return;
            }

            // logger(print_r(count($casesByResponsibleAtty), true));

            for ($i = 0; $i < count($casesByResponsibleAtty); $i++) {

                $elem = $casesByResponsibleAtty[$i];

                $responsibeAtty = $elem->ASALA_name ?? '';

                // logger(print_r($responsibeAtty, true));

                if ($responsibeAtty === '') {
                    continue;
                }

                if (isset($this->casesByResponsibeAtty[$responsibeAtty])) {
                    $this->casesByResponsibeAtty[$responsibeAtty]++;
                } else {
                    $this->casesByResponsibeAtty[$responsibeAtty] = 1;
                }
            }

            // logger(print_r($this->casesByResponsibeAtty, true));

            return $this->casesByResponsibeAtty;
        } catch (Throwable $th) {
            logger("Error : prepareCasesByResponsibileAtty_count" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesByResponsibileAtty($atty_name)
    {
        try {

            if (!($this->loadedCasesWithinTimeframe_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;

            $casesInGivenStep = $loadedCasesWithinTimeframe_query->where('action_step_attorneys_legal_assistants.ASALA_name', '=', $atty_name)->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->cursorPaginate(20);

            return $casesInGivenStep;
        } catch (Throwable $th) {
            logger("Error : prepareCasesByResponsibileAtty" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesWhereLastAccessedDateIsGreaterThan10Days_count()
    {
        try {
            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;
            $currentDate = new \DateTime();

            $last_10Days = clone $currentDate;
            $last_10Days->modify('-10 days');

            // logger(print_r($last_10Days, true));

            $casesAccessedWithin10DaysAgo = $loadedCasesWithinTimeframe_query->where('matters.matter_last_activity', '>=', $last_10Days)
                ->get();

            // logger(print_r($casesAccessedWithin10DaysAgo, true));
            // logger(print_r(count($casesAccessedWithin10DaysAgo), true));


            return count($casesAccessedWithin10DaysAgo) ?? 0;
        } catch (Throwable $th) {
            logger("Error : prepareCasesWhereLastAccessedDateIsGreaterThan10Days_count" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesWhereLastAccessedDateIsGreaterThan10Days()
    {
        try {

            if (!($this->loadedCasesWithinTimeframe_query ?? false)) {
                throw new Error('Query empty');
            }

            $currentDate = new \DateTime();
            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;


            $last_10Days = clone $currentDate;
            $last_10Days->modify('-10 days');

            $casesAccessedWithin10DaysAgo = $loadedCasesWithinTimeframe_query->where('matters.matter_last_activity', '>=', $last_10Days)
                ->select(
                    [
                        'matters.*',
                        'action_step_attorneys_legal_assistants.*',
                        'leads.*',
                        'a_s__steps.*'
                    ]
                )->cursorPaginate(20);

            return $casesAccessedWithin10DaysAgo;
        } catch (Throwable $th) {
            logger("Error : prepareCasesWhereLastAccessedDateIsGreaterThan10Days" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesWhereNextStepIsOverDue_count()
    {
        try {
            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;
            $currentDate = new \DateTime();
            $casesWithNextStepOverdued = $loadedCasesWithinTimeframe_query->where('matters.matter_next_task_due_date', '<', $currentDate)
                ->get();


            // logger(print_r("prepareCasesWhereNextStepIsOverDue_count", true));
            // logger(print_r(dd($loadedCasesWithinTimeframe_query->where('matters.matter_next_task_due_date', '<', $currentDate)->toSql()), true));
            // logger(print_r(count($casesWithNextStepOverdued), true));


            return count($casesWithNextStepOverdued) ?? 0;
        } catch (Throwable $th) {
            logger("Error : prepareCasesWhereNextStepIsOverDue_count" . $th->getMessage());
            return [];
        }
    }

    public function prepareCasesWhereNextStepIsOverDue()
    {
        try {
            $loadedCasesWithinTimeframe_query = clone $this->loadedCasesWithinTimeframe_query;
            $currentDate = new \DateTime();
            $casesWithNextStepOverdued = $loadedCasesWithinTimeframe_query->where('matters.matter_next_task_due_date', '<', $currentDate)
                ->select(
                    [
                        'matters.*',
                        'action_step_attorneys_legal_assistants.*',
                        'leads.*',
                        'a_s__steps.*'
                    ]
                )->cursorPaginate(20);

            // logger(print_r($casesWithNextStepOverdued, true));
            // logger(print_r(count($casesWithNextStepOverdued), true));


            return $casesWithNextStepOverdued;
        } catch (Throwable $th) {
            logger("Error : getNumberOfOpenCases" . $th->getMessage());
            return [];
        }
    }
}
