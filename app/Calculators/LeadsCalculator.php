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

class LeadsCalculator extends Calculator
{
    private ?Collection $loaded_All_LeadsCollection = null;
    private ?Collection $loadedLeadsCollection = null;
    private ?Collection $loadedRetainedLeadsCollection = null;
    private ?Collection $loadedIntakeScheduledStatus_changeLogCollection = null;
    private ?Collection $loadedConsultationScheduledStatus_changeLogCollection = null;
    private ?Collection $loadedRetainerMeetingConsultation_changeLogCollection = null;
    private ?Collection $loadedOpenLeads = null;

    private $loadedLeadsWithinTimeframe_query = null;

    private $timeframe;

    private $intake_step_identifier_on_lead_status = 'Intake Scheduled';
    private $consultatnScheduled_step_identifier_on_lead_status = "Consultation Scheduled";
    private $retainerMeetingScheduled_identifier_on_consultation_change_log = "Retainer Meeting Scheduled";

    private $retained_leads_identifier = [
        "Retained (Lead Converted)",
        "DO NOT USE (Retained)",
        "Closed (Retained)",
    ];

    public $newLeadsData = [];
    public $retainedLeadsData = [];
    public $retainedLeads_query = null;

    public $leads_averageDaysToRetained = 0;
    public $leads_averageDaysToIntakeSchedule = 0;
    public $leads_averageDaysToConsultationSchedule = 0;
    public $leads_averageDaysToRetainerMeeting = 0;
    public $leads_averageDaysLeadIsOpen = 0;

    public function setTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
    }

    public function loadAllLeads()
    {
        try {
            //code...
            switch ($this->timeframe) {
                case config('app.timeframes.all'):
                    $this->loaded_All_LeadsCollection = lead::get();
                    break;
            }

            // logger(print_r(count($this->loaded_All_LeadsCollection), true));
        } catch (Throwable $th) {
            logger(print_r("loadAllLeads Error " . $th->getMessage(), true));
            $this->loadedLeadsCollection = collect([]);
        }
    }

    public function loadLeadsWhereDateCreatedIsWithinTimespan($from = null, $to = null)
    {
        try {
            $loadedLeadsCollection_query = lead::where('leads.lead_date_created', '!=', null)
                ->leftJoin('lead_sources', 'leads.lead_source', '=', 'lead_sources.LS_id')
                ->leftJoin('matters', 'leads.lead_id', '=', 'matters.matter_lead')
                ->leftJoin('action_step_attorneys_legal_assistants', 'matters.matter_assigned_to', '=', 'action_step_attorneys_legal_assistants.ASALA_id')
                ->leftJoin('a_s__steps', 'matters.matter_current_step', '=', 'a_s__steps.step_id');

            if (($from ?? false)) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);
                    $loadedLeadsCollection_query = $loadedLeadsCollection_query->where('leads.lead_date_created', '>=', $fromDate);
                }
            }

            if (($to ?? false)) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);
                    $loadedLeadsCollection_query = $loadedLeadsCollection_query->where('leads.lead_date_created', '<=', $toDate);
                }
            }

            $this->loadedLeadsWithinTimeframe_query = clone $loadedLeadsCollection_query;
        } catch (Throwable $th) {
            logger(print_r("loadLeadsWhereDateCreatedIsWithinTimespan Error " . $th->getMessage(), true));
            $this->loadedLeadsCollection = collect([]);
        }
    }

    public function loadRetainedLeadsFromLeadsCreatedWithinTimespan($from = null, $to = null)
    {
        try {
            $this->retainedLeads_query = lead::select('matters.*', 'current_step_change_logs.*', 'a_s__steps.*', 'leads.*')
                ->leftJoin('matters', 'leads.lead_id', '=', 'matters.matter_lead')
                ->leftJoin('current_step_change_logs', 'matters.matter_id', '=', 'current_step_change_logs.CSCL_action_step_matter_id')
                ->leftJoin('a_s__steps', 'current_step_change_logs.CSCL_current_step', '=', 'a_s__steps.step_id')
                ->whereIn('a_s__steps.step_name', $this->retained_leads_identifier)
                ->where('current_step_change_logs.CSCL_change_date_time', '!=', null);

            if (($from ?? false)) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);

                    $this->retainedLeads_query = $this->retainedLeads_query->where('leads.lead_date_created', '>=', $fromDate);
                }
            }

            if (($to ?? false)) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $this->retainedLeads_query = $this->retainedLeads_query->where('leads.lead_date_created', '<=', $toDate);
                }
            }

            // $sql = $this->retainedLeads_query->toSql();
            // $bindings = $this->retainedLeads_query->getBindings();
            // logger(print_r($sql, true));
            // logger(print_r($bindings, true));

            $allLeads = $this->retainedLeads_query->get();
            // logger(print_r($allLeads, true));

            $this->loadedRetainedLeadsCollection = $allLeads->sortBy('CSCL_change_date_time')->unique('lead_id');

            // logger(print_r("Retained Collection: " . count($this->loadedRetainedLeadsCollection), true));
            // logger(print_r($this->loadedRetainedLeadsCollection, true));
        } catch (Throwable $th) {

            logger(print_r("loadCurrentStepChangeLogWithCurrentStepAsRetainer Error " . $th->getMessage(), true));
            $this->loadedRetainedLeadsCollection = collect([]);
        }
    }

    public function prepareNewLeads()
    {
        if (
            $this->loaded_All_LeadsCollection == null ||
            count($this->loaded_All_LeadsCollection ?? []) == 0
        ) {
            return;
        }

        for ($i = 0; $i < count($this->loaded_All_LeadsCollection); $i++) {
            $elem = $this->loaded_All_LeadsCollection[$i];

            $date = $elem->lead_date_created ?? false;

            if (!$date) {
                continue;
            }

            $date_Obj = new DateTime($date);
            $dateFormatted = $date_Obj->format('Y-m-d');

            $enochTimestamp = new DateTime($dateFormatted);
            $enochTimestamp =  $enochTimestamp->format('Uv');

            if (isset($this->newLeadsData[$enochTimestamp])) {
                $this->newLeadsData[$enochTimestamp]++;
            } else {
                $this->newLeadsData[$enochTimestamp] = 1;
            }
        }

        // logger(print_r($this->newLeadsData, true));
        ksort($this->newLeadsData);
    }

    public function prepareRetainedLeads()
    {

        // logger( print_r($this->loadedRetainedLeadsCollection, true) );
        if (
            $this->loadedRetainedLeadsCollection == null ||
            count($this->loadedRetainedLeadsCollection ?? []) == 0
        ) {
            $this->retainedLeadsData = [];
            return;
        }

        foreach ($this->loadedRetainedLeadsCollection as $key => $value) {
            # code...

            $elem = $value;

            $date = $elem->CSCL_change_date_time ?? false;

            if (!$date) {
                continue;
            }

            $date_Obj = new DateTime($date);
            $dateFormatted = $date_Obj->format('Y-m-d');

            $enochTimestamp = new DateTime($dateFormatted);
            $enochTimestamp =  $enochTimestamp->format('Uv');

            if (isset($this->retainedLeadsData[$enochTimestamp])) {
                $this->retainedLeadsData[$enochTimestamp]++;
            } else {
                $this->retainedLeadsData[$enochTimestamp] = 1;
            }
        }

        ksort($this->retainedLeadsData);
    }

    public function prepareLeadByCurrentStep_count($from, $to)
    {
        // logger(print_r(
        //     $from,
        //     true
        // ));
        // logger(print_r($to, true));

        $loadedLeadsCollection_query = clone $this->loadedLeadsWithinTimeframe_query;

        $leadByCurrentStep = $loadedLeadsCollection_query->leftJoin('current_step_change_logs', function (JoinClause $join) {
            $join->on('matters.matter_id', '=', 'current_step_change_logs.CSCL_action_step_matter_id')
                ->on('matters.matter_current_step', '=', 'current_step_change_logs.CSCL_current_step');
        });

        if (
            ($from ?? false)
        ) {
            if ($from !== 'all') {
                $fromDate = new DateTime($from);


                $leadByCurrentStep = $leadByCurrentStep->where('leads.lead_date_created', '>=', $fromDate);
            }
        }


        if (
            ($to ?? false)
        ) {
            if ($to !== 'all') {
                $toDate = new DateTime($to);

                $leadByCurrentStep = $leadByCurrentStep->where('leads.lead_date_created', '<=', $toDate);
            }
        }

        $leadByCurrentStep_data = $leadByCurrentStep
            ->select('leads.*', 'a_s__steps.*', 'matters.*', 'lead_sources.*')
            ->get();

        $leadByCurrentStep_data_sorted = $leadByCurrentStep_data->sortByDesc('matter_date_created')->unique('lead_id')->groupBy('step_name');

        $leadByCurrentStep_data_counted = [];

        foreach ($leadByCurrentStep_data_sorted as $key => $value) {
            # code...
            $lead_count = 0;
            if (count($value ?? []) > 0) {
                $lead_count = count($value);
            }

            array_push($leadByCurrentStep_data_counted, [
                'step_name' => $key,
                'matter_count' => $lead_count
            ]);
        }

        // logger(print_r($leadByCurrentStep_data_counted, true));
        // logger(print_r($leadByCurrentSte, true));
        // logger(print_r(count($leadByCurrentStep), true));


        return $leadByCurrentStep_data_counted;
    }

    public function prepareLeadBySource_count($from, $to)
    {

        $leads = clone $this->loadedLeadsWithinTimeframe_query;

        if (
            ($from ?? false)
        ) {
            if ($from !== 'all') {
                $fromDate = new DateTime($from);


                $leads = $leads->where('leads.lead_date_created', '>=', $fromDate);
            }
        }

        if (
            ($to ?? false)
        ) {
            if ($to !== 'all') {
                $toDate = new DateTime($to);

                $leads = $leads->where('leads.lead_date_created', '<=', $toDate);
            }
        }

        $leads = $leads->groupBy('LS_source')
            ->select(
                [
                    'lead_sources.LS_source as LS_source',
                    DB::raw('count(leads.lead_id) as lead_count'),

                ]
            )
            ->get();

        // logger(print_r($leads, true));
        // logger(print_r(count($leads), true));

        return $leads;
    }

    public function loadLeadStatusChangeLogWithCurrentStatusAsIntakeSheduled($from = null, $to = null)
    {
        try {
            //code...
            $loadedIntakeScheduledStatus_changeLogCollection_query = leadStatusChangeLog::leftJoin('lead_statuses', 'lead_status_change_logs.LSCL_current_status', '=', 'lead_statuses.LSt_id')
                ->leftJoin('matters', 'lead_status_change_logs.LSCL_action_step_matter_id', '=', 'matters.matter_id')
                ->leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->where('lead_status_change_logs.LSCL_change_date_time', '!=', null)
                ->where('lead_statuses.LSt_status', '=', $this->intake_step_identifier_on_lead_status);

            // $fullQuery = Str::replaceArray('?', $loadedIntakeScheduledStatus_changeLogCollection_query->getBindings(), $loadedIntakeScheduledStatus_changeLogCollection_query->toSql());

            // logger($fullQuery);

            if (
                ($from ?? false)
            ) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);


                    $loadedIntakeScheduledStatus_changeLogCollection_query = $loadedIntakeScheduledStatus_changeLogCollection_query->where('leads.lead_date_created', '>=', $fromDate);
                }
            }


            if (
                ($to ?? false)
            ) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $loadedIntakeScheduledStatus_changeLogCollection_query = $loadedIntakeScheduledStatus_changeLogCollection_query->where('leads.lead_date_created', '<=', $toDate);
                }
            }

            $this->loadedIntakeScheduledStatus_changeLogCollection = $loadedIntakeScheduledStatus_changeLogCollection_query->select(
                [
                    'lead_statuses.*',
                    'matters.*',
                    'leads.*',

                    'lead_status_change_logs.LSCL_previous_status as LSCL_previous_status',
                    'lead_status_change_logs.LSCL_current_status as LSCL_current_status',
                    'lead_status_change_logs.LSCL_action_step_lead_id as LSCL_action_step_lead_id',
                    'lead_status_change_logs.LSCL_change_date_time as LSCL_change_date_time',
                    'lead_status_change_logs.updated_at as LSCL_updated_at',

                ]
            )->get();

            $this->loadedIntakeScheduledStatus_changeLogCollection = $this->loadedIntakeScheduledStatus_changeLogCollection->sortBy('LSCL_change_date_time')->unique('lead_id');

            // logger(print_r("Intake Schedule Collection: " . count($this->loadedIntakeScheduledStatus_changeLogCollection), true));
        } catch (Throwable $th) {
            logger(print_r("loadLeadStatusChangeLogWithCurrentStatusAsIntakeSheduled Error " . $th->getMessage(), true));
            $this->loadedIntakeScheduledStatus_changeLogCollection = collect([]);
        }
    }

    public function loadLeadStatusChangeLogWithCurrentStatusAsConsultationScheduled($from = null, $to = null)
    {

        try {
            //code...
            $loadedConsultationScheduledStatus_changeLogCollection_query = leadStatusChangeLog::leftJoin('lead_statuses', 'lead_status_change_logs.LSCL_current_status', '=', 'lead_statuses.LSt_id')
                ->leftJoin('matters', 'lead_status_change_logs.LSCL_action_step_matter_id', '=', 'matters.matter_id')
                ->leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->where('lead_status_change_logs.LSCL_change_date_time', '!=', null)
                ->where('lead_statuses.LSt_status', '=', $this->consultatnScheduled_step_identifier_on_lead_status);

            if (
                ($from ?? false)
            ) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);


                    $loadedConsultationScheduledStatus_changeLogCollection_query = $loadedConsultationScheduledStatus_changeLogCollection_query->where('leads.lead_date_created', '>=', $fromDate);
                }
            }


            if (
                ($to ?? false)
            ) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $loadedConsultationScheduledStatus_changeLogCollection_query = $loadedConsultationScheduledStatus_changeLogCollection_query->where('leads.lead_date_created', '<=', $toDate);
                }
            }

            $this->loadedConsultationScheduledStatus_changeLogCollection = $loadedConsultationScheduledStatus_changeLogCollection_query->select(
                [
                    'lead_statuses.*',
                    'matters.*',
                    'leads.*',

                    'lead_status_change_logs.LSCL_previous_status as LSCL_previous_status',
                    'lead_status_change_logs.LSCL_current_status as LSCL_current_status',
                    'lead_status_change_logs.LSCL_action_step_lead_id as LSCL_action_step_lead_id',
                    'lead_status_change_logs.LSCL_change_date_time as LSCL_change_date_time',
                    'lead_status_change_logs.updated_at as LSCL_updated_at',

                ]
            )->get();

            $this->loadedConsultationScheduledStatus_changeLogCollection = $this->loadedConsultationScheduledStatus_changeLogCollection->sortBy('LSCL_change_date_time')->unique('lead_id');

            // logger(print_r("Consultation Schedule Collection: " . count($this->loadedConsultationScheduledStatus_changeLogCollection), true));
        } catch (Throwable $th) {

            logger(print_r("loadLeadStatusChangeLogWithCurrentStatusAsConsultationScheduled Error " . $th->getMessage(), true));
            $this->loadedConsultationScheduledStatus_changeLogCollection = collect([]);
        }
    }

    public function loadConsultationChangeLogWithCCL_OutcomeAsRetainerMeetingScheduled($from = null, $to = null)
    {
        try {
            //code...
            $loadedRetainerMeetingConsultation_changeLogCollection_query = consultationChangeLog::leftJoin('matters', 'consultation_change_logs.CCL_action_step_matter_id', '=', 'matters.matter_id')
                ->leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->where('consultation_change_logs.CCL_schedule_date', '!=', null)
                ->where('consultation_change_logs.CCL_outcome', '=', $this->retainerMeetingScheduled_identifier_on_consultation_change_log);

            if (
                ($from ?? false)
            ) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);


                    $loadedRetainerMeetingConsultation_changeLogCollection_query = $loadedRetainerMeetingConsultation_changeLogCollection_query->where('leads.lead_date_created', '>=', $fromDate);
                }
            }


            if (
                ($to ?? false)
            ) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $loadedRetainerMeetingConsultation_changeLogCollection_query = $loadedRetainerMeetingConsultation_changeLogCollection_query->where('leads.lead_date_created', '<=', $toDate);
                }
            }

            $this->loadedRetainerMeetingConsultation_changeLogCollection = $loadedRetainerMeetingConsultation_changeLogCollection_query->select(
                [
                    'matters.*',
                    'leads.*',
                    'consultation_change_logs.*',
                ]
            )->get();

            $this->loadedRetainerMeetingConsultation_changeLogCollection = $this->loadedRetainerMeetingConsultation_changeLogCollection->unique('lead_id');

            // logger(print_r("Retainer meeting: " . count($this->loadedRetainerMeetingConsultation_changeLogCollection), true));
        } catch (Throwable $th) {
            logger(print_r("loadConsultationChangeLogWithCCL_OutcomeAsRetainerMeetingScheduled Error " . $th->getMessage(), true));
            $this->loadedRetainerMeetingConsultation_changeLogCollection = collect([]);
        }
    }

    public function loadOpenLeads($from = null, $to = null)
    {
        try {
            //code...
            $this->loadedOpenLeads = Matter::select('matters.matter_id', 'matters.matter_date_created', 'lead_status_change_logs.LSCL_id', 'lead_status_change_logs.LSCL_action_step_matter_id', 'lead_status_change_logs.LSCL_current_status')
                ->leftJoin('lead_status_change_logs', 'matters.matter_id', '=', 'lead_status_change_logs.LSCL_action_step_matter_id')
                ->whereNotIn('matters.matter_id', function (Builder $query) {
                    $query->select('matters.matter_id')
                        ->from('matters')
                        ->leftJoin('lead_status_change_logs', 'matters.matter_id', '=', 'lead_status_change_logs.LSCL_action_step_matter_id')
                        ->leftJoin('lead_statuses', 'lead_status_change_logs.LSCL_current_status', '=', 'lead_statuses.LSt_id')
                        ->whereRaw('LOWER(lead_statuses.LSt_status) LIKE ?', ['%close%']);
                })
                ->whereIn('matters.matter_id', function (Builder $query) {
                    $query->selectRaw('DISTINCT LSCL_action_step_matter_id')
                        ->from('lead_status_change_logs');
                })
                ->orderBy('matters.matter_id', 'ASC')
                ->get();

            // dd($openLeads->toSql(), $openLeads->getBindings());

            // $this->loadedOpenLeads = $openLeads->unique('matter_id');
            // logger(print_r("Open Leads: " . $this->loadedOpenLeads, true));
            // logger(print_r("Open Leads: " . count($this->loadedOpenLeads), true));

            if (
                ($from ?? false)
            ) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);
                    $this->loadedOpenLeads = $this->loadedOpenLeads
                        ->where('matter_date_created', '!=', null)
                        ->where('matter_date_created', '>=', $fromDate);
                }
            }

            if (
                ($to ?? false)
            ) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);
                    $this->loadedOpenLeads = $this->loadedOpenLeads
                        ->where('matter_date_created', '!=', null)
                        ->where('matter_date_created', '<=', $toDate);
                }
            }

            // logger(print_r("Open Leads: " . count($this->loadedOpenLeads), true));
        } catch (Throwable $th) {
            $this->loadedOpenLeads = collect([]);

            logger(print_r("loadOpenLeads Error " . $th->getMessage(), true));
        }
    }

    public function prepareAverageTime_newLead_to_retained()
    {
        try {
            //code...
            $totalDays = 0;
            $totalRetainedLeadsCounted = 0;

            if (
                $this->loadedRetainedLeadsCollection == null ||
                count($this->loadedRetainedLeadsCollection ?? []) == 0
            ) {
                $this->leads_averageDaysToRetained = 0;
                return;
            }

            foreach ($this->loadedRetainedLeadsCollection as $key => $value) {
                $date_retainedEntryWasRegisteredInDatabase = $value->CSCL_change_date_time ?? false;

                $leadCreatedDate = $value->lead_date_created  ?? false;

                // logger(print_r("'date_retainedEntryWasRegisteredInDatabase ' $date_retainedEntryWasRegisteredInDatabase", true));
                // logger(print_r("'leadCreatedDate ' $leadCreatedDate", true));


                if (!$date_retainedEntryWasRegisteredInDatabase || !$leadCreatedDate) {
                    continue;
                }

                $totalRetainedLeadsCounted++;

                $date_retainedEntryWasRegisteredInDatabase_Obj = new DateTime($date_retainedEntryWasRegisteredInDatabase);
                $leadCreatedDate_Obj = new DateTime($leadCreatedDate);

                $interval = $leadCreatedDate_Obj->diff($date_retainedEntryWasRegisteredInDatabase_Obj);
                $daysDifference = abs($interval->format('%a'));

                $totalDays = $totalDays + $daysDifference;

                // logger(print_r($interval, true));
            }

            if (
                $totalRetainedLeadsCounted == 0 ||
                $totalDays == 0
            ) {
                logger("Denominator is zero, cannot divide.");
                throw new Error('calculation error: result undefined');
            }

            $result = $totalDays / $totalRetainedLeadsCounted;
            $result = floatval($result); // Convert the result to a number (float)

            $result = ceil($result * 100) / 100;
            // logger(print_r($result, true));

            $this->leads_averageDaysToRetained = $result;
        } catch (Throwable $th) {
            $this->leads_averageDaysToRetained = "undefined";
            logger("Error : prepareAverageTime_newLead_to_retained");
            logger($th->getMessage());
        }
    }

    public function prepareAverageTime_newLead_to_intake_on_leadStatus()
    {
        try {
            //code...
            $totalDays = 0;
            $totalIntakeLeadStatusCounted = 0;

            if (!($this->loadedIntakeScheduledStatus_changeLogCollection ?? false)) {
                $this->leads_averageDaysToIntakeSchedule = 0;
                return;
            }

            // logger(print_r($this->loadedIntakeScheduledStatus_changeLogCollection, true));

            foreach ($this->loadedIntakeScheduledStatus_changeLogCollection as $key => $value) {
                $elem = $value;

                $date_intakeScheduleWasRegisteredInDatabase = $elem->LSCL_change_date_time ?? false;

                $matterCreatedDate = $elem->matter_date_created  ?? false;

                // logger(print_r("'elem ' $elem", true));
                // logger(print_r("'date_intakeScheduleWasRegisteredInDatabase ' $date_intakeScheduleWasRegisteredInDatabase", true));
                // logger(print_r("'matterCreatedDate ' $matterCreatedDate", true));


                if (!$date_intakeScheduleWasRegisteredInDatabase || !$matterCreatedDate) {
                    // logger(print_r("here", true));

                    continue;
                }

                $totalIntakeLeadStatusCounted++;

                $date_intakeScheduleWasRegisteredInDatabase_Obj = new DateTime($date_intakeScheduleWasRegisteredInDatabase);
                $matterCreatedDate_Obj = new DateTime($matterCreatedDate);

                $interval = $matterCreatedDate_Obj->diff($date_intakeScheduleWasRegisteredInDatabase_Obj);
                $daysDifference = abs($interval->format('%a'));

                $totalDays = $totalDays + $daysDifference;

                // logger(print_r($interval, true));
            }

            // logger(print_r($totalDays, true));
            // logger(print_r($totalIntakeLeadStatusCounted, true));

            // $totalDays = 0;
            if (
                $totalIntakeLeadStatusCounted == 0 ||
                $totalDays == 0
            ) {
                logger("Denominator is zero, cannot divide.");
                throw new Error('calculation error: result undefined');
            }

            $result = $totalDays / $totalIntakeLeadStatusCounted;
            $result = floatval($result); // Convert the result to a number (float)

            $result = ceil($result * 100) / 100;
            // logger(print_r($result, true));

            $this->leads_averageDaysToIntakeSchedule = $result;
        } catch (Throwable $th) {
            $this->leads_averageDaysToIntakeSchedule = "undefined";
            logger("Error : prepareAverageTime_newLead_to_intake_on_leadStatus");
            logger(print_r($th->getMessage(), true));
        }
    }

    public function prepareAverageTime_newLead_to_consultation_scheduled_on_leadStatus()
    {
        try {
            //code...
            $totalDays = 0;
            $totalconsultationScheduledLeadStatusCounted = 0;

            if (!($this->loadedConsultationScheduledStatus_changeLogCollection ?? false)) {
                $this->leads_averageDaysToConsultationSchedule = 0;
                return;
            }

            foreach ($this->loadedConsultationScheduledStatus_changeLogCollection as $key => $value) {
                $elem = $value;

                $date_consultationScheduleWasRegisteredInDatabase = $elem->LSCL_change_date_time ?? false;

                $matterCreatedDate = $elem->matter_date_created  ?? false;

                // logger(print_r("'elem ' $elem", true));
                // logger(print_r("'date_consultationScheduleWasRegisteredInDatabase ' $date_consultationScheduleWasRegisteredInDatabase", true));
                // logger(print_r("'matterCreatedDate ' $matterCreatedDate", true));


                if (!$date_consultationScheduleWasRegisteredInDatabase || !$matterCreatedDate) {
                    // logger(print_r("here", true));

                    continue;
                }

                $totalconsultationScheduledLeadStatusCounted++;

                $date_consultationScheduleWasRegisteredInDatabase_Obj = new DateTime($date_consultationScheduleWasRegisteredInDatabase);
                $matterCreatedDate_Obj = new DateTime($matterCreatedDate);

                $interval = $matterCreatedDate_Obj->diff($date_consultationScheduleWasRegisteredInDatabase_Obj);
                $daysDifference = abs($interval->format('%a'));

                $totalDays = $totalDays + $daysDifference;

                // logger(print_r($interval, true));
            }

            // logger(print_r($totalDays, true));
            // logger(print_r($totalconsultationScheduledLeadStatusCounted, true));

            // $totalDays = 0;
            if (
                $totalconsultationScheduledLeadStatusCounted == 0 ||
                $totalDays == 0
            ) {
                logger("Denominator is zero, cannot divide.");
                throw new Error('calculation error: result undefined');
            }

            $result = $totalDays / $totalconsultationScheduledLeadStatusCounted;
            $result = floatval($result); // Convert the result to a number (float)

            $result = ceil($result * 100) / 100;
            // logger(print_r($result, true));

            $this->leads_averageDaysToConsultationSchedule = $result;
        } catch (Throwable $th) {
            $this->leads_averageDaysToConsultationSchedule = "undefined";
            logger("Error : prepareAverageTime_newLead_to_consultation_scheduled_on_leadStatus");
        }
    }

    public function prepareAverageTime_newLead_to_retainer_meeting_on_consultation_change_log()
    {
        try {
            //code...
            $totalDays = 0;
            $totalretainermeetingscheduledCounted = 0;

            if (!($this->loadedRetainerMeetingConsultation_changeLogCollection ?? false)) {
                $this->leads_averageDaysToRetainerMeeting = 0;
                return;
            }

            foreach ($this->loadedRetainerMeetingConsultation_changeLogCollection as $key => $value) {
                $elem = $value;

                $date_retainerMeetingWasScheduledInDatabase = $elem->CCL_schedule_date ?? false;

                $matterCreatedDate = $elem->matter_date_created  ?? false;

                // logger(print_r("'elem ' $elem", true));
                // logger(print_r("'date_retainerMeetingWasScheduledInDatabase ' $date_retainerMeetingWasScheduledInDatabase", true));
                // logger(print_r("'matterCreatedDate ' $matterCreatedDate", true));


                if (!$date_retainerMeetingWasScheduledInDatabase || !$matterCreatedDate) {
                    // logger(print_r("here", true));

                    continue;
                }

                $totalretainermeetingscheduledCounted++;

                $date_retainerMeetingWasScheduledInDatabase_Obj = new DateTime($date_retainerMeetingWasScheduledInDatabase);
                $matterCreatedDate_Obj = new DateTime($matterCreatedDate);

                $interval = $matterCreatedDate_Obj->diff($date_retainerMeetingWasScheduledInDatabase_Obj);
                $daysDifference = abs($interval->format('%a'));

                $totalDays = $totalDays + $daysDifference;

                // logger(print_r($interval, true));
            }

            // logger(print_r($totalDays, true));
            // logger(print_r($totalretainermeetingscheduledCounted, true));

            // $totalDays = 0;
            if (
                $totalretainermeetingscheduledCounted == 0 ||
                $totalDays == 0
            ) {
                logger("Denominator is zero, cannot divide.");
                throw new Error('calculation error: result undefined');
            }

            $result = $totalDays / $totalretainermeetingscheduledCounted;
            $result = floatval($result); // Convert the result to a number (float)

            $result = ceil($result * 100) / 100;
            // logger(print_r($result, true));

            $this->leads_averageDaysToRetainerMeeting = $result;
        } catch (Throwable $th) {
            $this->leads_averageDaysToRetainerMeeting = "undefined";
            logger("Error : prepareAverageTime_newLead_to_retainer_meeting_on_consultation_change_log");
        }
    }

    public function prepareAverageTime_Lead_is_open()
    {
        try {
            //code...
            $totalDays = 0;
            $totalOpenLeadCounted = 0;

            if (!($this->loadedOpenLeads ?? false)) {
                $this->leads_averageDaysToRetainerMeeting = 0;
                return;
            }

            for ($i = 0; $i < count($this->loadedOpenLeads); $i++) {

                $elem = $this->loadedOpenLeads[$i];

                $matterCreatedDate = $elem->matter_date_created  ?? false;


                if (!$matterCreatedDate) {
                    // logger(print_r("here", true));

                    continue;
                }

                $totalOpenLeadCounted++;

                $todaysDate = new DateTime();
                $matterCreatedDate_Obj = new DateTime($matterCreatedDate);

                $interval = $matterCreatedDate_Obj->diff($todaysDate);
                $daysDifference = abs($interval->format('%a'));

                $totalDays = $totalDays + $daysDifference;

                // logger(print_r($interval, true));
            }

            // logger(print_r($totalDays, true));
            // logger(print_r($totalOpenLeadCounted, true));

            if (
                $totalOpenLeadCounted == 0 ||
                $totalDays == 0
            ) {
                logger("Denominator is zero, cannot divide.");
                throw new Error('calculation error: result undefined');
            }

            $result = $totalDays / $totalOpenLeadCounted;
            $result = floatval($result); // Convert the result to a number (float)

            $result = ceil($result * 100) / 100;
            // logger(print_r($result, true));

            $this->leads_averageDaysLeadIsOpen = $result;
        } catch (Throwable $th) {
            $this->leads_averageDaysLeadIsOpen = "undefined";
            logger("Error : prepareAverageTime_newLead_to_retainer_meeting_on_consultation_change_log");
        }
    }

    public function getLeadsByStep($stepname)
    {
        try {

            if (!($this->loadedLeadsWithinTimeframe_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedLeadsWithinTimeframe_query = clone $this->loadedLeadsWithinTimeframe_query;
            $casesInGivenStep = $loadedLeadsWithinTimeframe_query->where('a_s__steps.step_name', '=', $stepname)->cursorPaginate(20);

            foreach ($casesInGivenStep as $case) {
                // Lazy load the 'matters' relationship, if not already loaded
                $lead_id = $case->lead_id ?? null;

                if ($lead_id != null) {
                    $case['matters'] = matter::where('matter_lead', '=', $lead_id)
                        ->with(['currentStep', 'currentMatterAttorney'])
                        ->select(
                            "matters.*",
                        )->get();
                }
            }

            return $casesInGivenStep;
        } catch (Throwable $th) {
            logger("Error : getCasesByStep" . $th->getMessage());
            return [];
        }
    }

    public function getLeadsBySource($source)
    {
        try {

            if (!($this->loadedLeadsWithinTimeframe_query ?? false)) {
                throw new Error('Query empty');
            }


            $loadedLeadsWithinTimeframe_query = clone $this->loadedLeadsWithinTimeframe_query;
            $leadsWithSource = $loadedLeadsWithinTimeframe_query
                ->where('lead_sources.LS_source', '=', $source)
                ->cursorPaginate(20);

            foreach ($leadsWithSource as $lead) {
                // Lazy load the 'matters' relationship, if not already loaded
                $lead_id = $lead->lead_id ?? null;

                if ($lead_id != null) {
                    $lead['matters'] = matter::where('matter_lead', '=', $lead_id)
                        ->with(['currentStep', 'currentMatterAttorney'])
                        ->select(
                            "matters.*",
                        )->get();
                }
            }

            return $leadsWithSource;
        } catch (Throwable $th) {
            logger("Error : getLeadsBySource" . $th->getMessage());
            return [];
        }
    }

    public function getSelectedRetainedLeads($from = null, $to = null)
    {
        try {

            $retainedLeads_query = lead::select('matters.*', 'current_step_change_logs.*', 'a_s__steps.*', 'leads.*')
                ->leftJoin('matters', 'leads.lead_id', '=', 'matters.matter_lead')
                ->leftJoin('current_step_change_logs', 'matters.matter_id', '=', 'current_step_change_logs.CSCL_action_step_matter_id')
                ->leftJoin('a_s__steps', 'current_step_change_logs.CSCL_current_step', '=', 'a_s__steps.step_id')
                ->whereIn('a_s__steps.step_name', $this->retained_leads_identifier)
                ->where('current_step_change_logs.CSCL_change_date_time', '!=', null);

            if (($from ?? false)) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);

                    $retainedLeads_query = $retainedLeads_query->where('current_step_change_logs.CSCL_change_date_time', '>=', $fromDate);
                }
            }

            if (($to ?? false)) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $retainedLeads_query = $retainedLeads_query->where('current_step_change_logs.CSCL_change_date_time', '<=', $toDate);
                }
            }

            $allLeads = $retainedLeads_query->get();
            $loadedRetainedLeadsCollection = $allLeads->sortBy('CSCL_change_date_time')->unique('lead_id');

            if (
                $loadedRetainedLeadsCollection == null ||
                count($loadedRetainedLeadsCollection ?? []) == 0
            ) {
                throw new Error('empty base collection');
            }

            $lead_ids = $loadedRetainedLeadsCollection->pluck('lead_id');

            $retained_leads = lead::leftJoin('lead_sources', 'leads.lead_source', '=', 'lead_sources.LS_id')
                ->whereIn('leads.lead_id', $lead_ids)
                ->cursorPaginate(20);


            foreach ($retained_leads as $lead) {
                // Lazy load the 'matters' relationship, if not already loaded
                $lead_id = $lead->lead_id ?? null;

                if ($lead_id != null) {
                    $lead['matters'] = matter::where('matter_lead', '=', $lead_id)
                        ->with(['currentStep', 'currentMatterAttorney'])
                        ->select(
                            "matters.*",
                        )->get();
                }
            }

            return $retained_leads;
        } catch (Throwable $th) {
            logger("Error : getSelectedRetainedLeads" . $th->getMessage());
            return [];
        }
    }

    public function getSelectedNewLeads()
    {
        try {

            if (!($this->loadedLeadsWithinTimeframe_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedLeadsWithinTimeframe_query = clone $this->loadedLeadsWithinTimeframe_query;
            $newLeadsData = $loadedLeadsWithinTimeframe_query
                ->cursorPaginate(20);

            foreach ($newLeadsData as $lead) {
                // Lazy load the 'matters' relationship, if not already loaded
                $lead_id = $lead->lead_id ?? null;

                if ($lead_id != null) {
                    $lead['matters'] = matter::where('matter_lead', '=', $lead_id)
                        ->with(['currentStep', 'currentMatterAttorney'])
                        ->select(
                            "matters.*",
                        )->get();
                }
            }

            return $newLeadsData;
        } catch (Throwable $th) {
            logger("Error : getLeadsBySource" . $th->getMessage());
            return [];
        }
    }
}
