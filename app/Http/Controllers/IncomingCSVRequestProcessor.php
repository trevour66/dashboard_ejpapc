<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

use League\Csv\Reader;
use League\Csv\Statement;

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
use Exception;
use Faker\Core\Number;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class IncomingCSVRequestProcessor extends Controller
{
    private $matterDataBeforeUpdate = null;
    private $leadDataBeforeUpdate = null;
    private $createdOrUpdatedMatter = null;
    private $createdOrUpdatedLead =  null;

    private function createOrUpdateIndependentTableData($dataFieldName, $dataValue, Model $model)
    {
        try {
            //code...
            if ($dataValue ?? false) {
                $data = array();

                $data[$dataFieldName] = $dataValue;

                $createdOrUpdatedModel = $model->updateOrCreate(
                    $data,
                    $data,
                );

                return $createdOrUpdatedModel;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    private function addDaysToDate($date, $days)
    {
        try {
            if (!($date ?? false) || !($days ?? false) || str_contains($days, "-")) {
                return false;
            }

            $dateTime = new \DateTime($date);
            $dateTime->modify("+$days days");

            $modifiedDate = $dateTime->format('Y-m-d H:i:s');

            return $modifiedDate;
        } catch (Exception $e) {
            logger("here error");
            logger($e->getMessage());
            return false;
        }
    }

    private function parseCommonDate($dateValue)
    {

        try {
            if ($dateValue !== null && $dateValue !== "") {
                $dateTime = new \DateTime($dateValue);

                return $dateTime->format('Y-m-d H:i:s');
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            logger($th->getMessage());
            return false;
        }
    }

    private function createOrUpdateMatterByActionStepMatterID($matterRecord)
    {
        $createdOrUpdatedMatterType = $this->createOrUpdateIndependentTableData('MT_type', $matterRecord['Matter Type'], new matterType()) ?? false;
        $createdOrUpdatedAS_Step = $this->createOrUpdateIndependentTableData('step_name', $matterRecord['Current Step'], new AS_Step()) ?? false;
        $createdOrUpdatedMatterStatus = $this->createOrUpdateIndependentTableData('MSt_status', $matterRecord['Matter Status'], new matterStatus()) ?? false;
        $createdOrUpdatedMatterAttorrney = $this->createOrUpdateIndependentTableData('ASALA_name', $matterRecord['Assigned To'], new actionStep_attorneys_legalAssistant()) ?? false;

        if (!($matterRecord['ID'] ?? false)) {
            return false;
        }

        $data = array();

        $finder_data = array();
        $finder_data['matter_actionstep_id'] = $matterRecord['ID'];


        if (($createdOrUpdatedAS_Step->step_id ?? false)) $data['matter_current_step'] = $createdOrUpdatedAS_Step->step_id;
        if (($createdOrUpdatedMatterType->MT_id ?? false)) $data['matter_current_matter_type'] = $createdOrUpdatedMatterType->MT_id;
        if (($createdOrUpdatedMatterStatus->MSt_id ?? false)) $data['matter_current_status'] = $createdOrUpdatedMatterStatus->MSt_id;
        if (($createdOrUpdatedMatterAttorrney->ASALA_id ?? false)) $data['matter_assigned_to'] = $createdOrUpdatedMatterAttorrney->ASALA_id;


        if (Str::ascii($matterRecord['Matter Name']) ?? false) {
            $data['matter_current_name'] = Str::ascii($matterRecord['Matter Name']);
        }
        if (Str::ascii($matterRecord['Demand Type']) ?? false) {
            $data['matter_demand_letter_type'] = Str::ascii($matterRecord['Demand Type']);
        }
        if (Str::ascii($matterRecord['Last Filenote']) ?? false) {
            $data['matter_last_filenote'] = Str::ascii($matterRecord['Last Filenote']);
        }
        if (Str::ascii($matterRecord['Next Task']) ?? false) {
            $data['matter_next_task'] = Str::ascii($matterRecord['Next Task']);
        }

        // matter_date_created',
        $matter_date_created = $this->parseCommonDate($matterRecord['Date Created']);
        if ($matter_date_created ?? false) {
            $data['matter_date_created'] = $matter_date_created;
        }

        // 'matter_tentv_settlement_date',
        $matter_tentv_settlement_date = $this->parseCommonDate($matterRecord['Tentv Settlement Date']);
        if ($matter_tentv_settlement_date ?? false) {
            $data['matter_tentv_settlement_date'] = $matter_tentv_settlement_date;
        }
        // 'matter_settlement_agreement_signed',
        $matter_settlement_agreement_signed = $this->parseCommonDate($matterRecord['SA Signed']);
        if ($matter_settlement_agreement_signed ?? false) {
            $data['matter_settlement_agreement_signed'] = $matter_settlement_agreement_signed;
        }
        // 'matter_settlement_funds_expected_date',
        $matter_settlement_funds_expected_date = $this->parseCommonDate($matterRecord['Settlement Funds Expected Date']);
        if ($matter_settlement_funds_expected_date ?? false) {
            $data['matter_settlement_funds_expected_date'] = $matter_settlement_funds_expected_date;
        }
        // 'matter_settlement_funds_received_date',
        $matter_settlement_funds_received_date = $this->parseCommonDate($matterRecord['Funds Rcvd']);
        if ($matter_settlement_funds_received_date ?? false) {
            $data['matter_settlement_funds_received_date'] = $matter_settlement_funds_received_date;
        }
        // 'matter_last_activity',
        $matter_last_activity = $this->parseCommonDate($matterRecord['Last Activity']);
        if ($matter_last_activity ?? false) {
            $data['matter_last_activity'] = $matter_last_activity;
        }
        // 'matter_next_task_due_date',
        $matter_next_task_due_date = $this->parseCommonDate($matterRecord['Next Task Due']);
        if ($matter_next_task_due_date ?? false) {
            $data['matter_next_task_due_date'] = $matter_next_task_due_date;
        }
        // 'matter_close_date',
        $matter_close_date = $this->parseCommonDate($matterRecord['Date Closed']);
        if ($matter_close_date ?? false) {
            $data['matter_close_date'] = $matter_close_date;
        }
        // matter_current_status_started_date
        $matter_current_status_started_date = $this->parseCommonDate($matterRecord['Current Status Date']);
        if ($matter_current_status_started_date ?? false) {
            $data['matter_current_status_started_date'] = $matter_current_status_started_date;
        }

        // 'matter_atty_fees',
        $matter_atty_fees = $this->parseMoneyToDecimal($matterRecord['Atty Fees']);
        if ($matter_atty_fees ?? false) {
            $data['matter_atty_fees'] = $matter_atty_fees;
        }
        // 'matter_current_offer',
        $matter_current_offer = $this->parseMoneyToDecimal($matterRecord['Current Offer']);
        if ($matter_current_offer ?? false) {
            $data['matter_current_offer'] = $matter_current_offer;
        }

        $data = array_merge($finder_data, $data);

        $newMatter = matter::updateOrCreate(
            $finder_data,
            $data
        );

        return $newMatter;
    }

    private function parseDayFromString($day_ago_string_or_date, $stringType)
    {
        try {
            //code...
            if (!$day_ago_string_or_date ?? false) {
                return false;
            }
            switch ($stringType) {
                case 'DAYS_AGO':
                    $interval = \DateInterval::createFromDateString($day_ago_string_or_date);
                    $days = $interval->d;

                    return $days;
                    break;

                default:
                    # code...
                    break;
            }
        } catch (\Throwable $th) {
            logger($th->getMessage());
            return false;
        }
    }

    private function parseMoneyToDecimal($string)
    {
        try {
            $pattern = '/^\$[\d,]+\.\d{2}$/';
            if (($string ?? false) && preg_match($pattern, $string)) {
                // Remove dollar sign and commas
                $string = str_replace('$', '', $string);
                $string = str_replace(',', '', $string);

                // Parse as float
                $decimal = (float) $string;

                return $decimal;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            logger($th->getMessage());
            return false;
        }
    }

    private function createOrUpdateLeadByLeadEmail($matterRecord)
    {
        $createdOrUpdatedLeadOverallStatus = $this->createOrUpdateIndependentTableData('LOS_status', $matterRecord['Lead Overall Status'], new leadOverallStatus()) ?? false;

        $createdOrUpdatedLeadSource = $this->createOrUpdateIndependentTableData('LS_source', $matterRecord['Lead Source'], new leadSource()) ?? false;
        $createdOrUpdatedLeadStatus = $this->createOrUpdateIndependentTableData('LSt_status', $matterRecord['Lead Status'], new leadStatus()) ?? false;

        if (!($matterRecord['Primary Participant Email'] ?? false)) {
            return false;
        }

        $data = array();

        $finder_data = array();
        $finder_data['lead_email'] = $matterRecord['Primary Participant Email'];

        if (($createdOrUpdatedLeadOverallStatus->LOS_id ?? false)) $data['lead_current_overall_lead_status'] = $createdOrUpdatedLeadOverallStatus->LOS_id;
        if (($createdOrUpdatedLeadSource->LS_id ?? false)) $data['lead_source'] = $createdOrUpdatedLeadSource->LS_id;
        if (($createdOrUpdatedLeadStatus->LSt_id ?? false)) $data['lead_current_status'] = $createdOrUpdatedLeadStatus->LSt_id;

        if (Str::ascii($matterRecord['Primary Participant']) ?? false) {
            $data['lead_name'] = Str::ascii($matterRecord['Primary Participant']);
        }
        if (Str::ascii($matterRecord['Clt Zip Code']) ?? false) {
            $data['lead_zipcode'] = Str::ascii($matterRecord['Clt Zip Code']);
        }

        // lead_date_created',
        $lead_date_created = $this->parseCommonDate($matterRecord['Lead created date']);
        if ($lead_date_created ?? false) {
            $data['lead_date_created'] = $lead_date_created;
        }

        $data = array_merge($finder_data, $data);

        $newLead = lead::updateOrCreate(
            $finder_data,
            $data
        );

        return $newLead;
    }

    private function linkLeadToMatter(matter $createdOrUpdatedMatter,  $createdOrUpdatedLead)
    {
        if (!($createdOrUpdatedLead ?? false) && !($createdOrUpdatedMatter ?? false)) {
            return;
        }

        if ($createdOrUpdatedLead->lead_id ?? false) {
            $data = array();

            $finder_data = array();
            $finder_data['matter_actionstep_id'] = $createdOrUpdatedMatter->matter_actionstep_id;

            $data['matter_lead'] = Str::ascii($createdOrUpdatedLead->lead_id);

            $data = array_merge($finder_data, $data);

            matter::updateOrCreate(
                $finder_data,
                $data
            );
        }
    }

    private function createLeadOverallStatusChangeLogTableData($leadDataBeforeUpdate, $createdOrUpdatedLead, $createdOrUpdatedMatter)
    {
        $createdOrUpdatedLead_LOS_id = $createdOrUpdatedLead->currentLeadOverallStatus->LOS_id ?? false;
        $leadDataBeforeUpdate_LOS_id = $leadDataBeforeUpdate->currentLeadOverallStatus->LOS_id ?? null;

        if (
            ($createdOrUpdatedMatter->matter_id ?? false) &&
            ($createdOrUpdatedLead_LOS_id ?? false) &&
            ($createdOrUpdatedLead_LOS_id != $leadDataBeforeUpdate_LOS_id) &&
            ($createdOrUpdatedLead->lead_id ?? false)
        ) {

            leadOverallStatusChangeLog::create(
                [
                    'LOSCL_prev_ov_status' => $leadDataBeforeUpdate_LOS_id,
                    'LOSCL_active_ov_status' => $createdOrUpdatedLead_LOS_id,
                    'LOSCL_as_lead_id' => $createdOrUpdatedLead->lead_id,
                    'LOSCL_as_matter_id' => $createdOrUpdatedMatter->matter_id,
                    'LOSCL_change_status' => ($leadDataBeforeUpdate_LOS_id) ? 'REAL_CHANGE' : 'INITIAL',
                ]
            );
        }
    }

    private function createLeadStatusChangeLogTableData($leadDataBeforeUpdate, $createdOrUpdatedLead, $createdOrUpdatedMatter)
    {
        $createdOrUpdatedLead_LSt_id = $createdOrUpdatedLead->currentLeadStatus->LSt_id ?? false;
        $leadDataBeforeUpdate_LSt_id = $leadDataBeforeUpdate->currentLeadStatus->LSt_id ?? null;

        if (
            ($createdOrUpdatedMatter->matter_id ?? false) &&
            ($createdOrUpdatedLead_LSt_id ?? false) &&
            ($createdOrUpdatedLead_LSt_id != $leadDataBeforeUpdate_LSt_id) &&
            ($createdOrUpdatedLead->lead_id ?? false)
        ) {

            leadStatusChangeLog::create(
                [
                    'LSCL_previous_status' => $leadDataBeforeUpdate_LSt_id,
                    'LSCL_current_status' => $createdOrUpdatedLead_LSt_id,
                    'LSCL_action_step_lead_id' => $createdOrUpdatedLead->lead_id,
                    'LSCL_action_step_matter_id' => $createdOrUpdatedMatter->matter_id,
                    'LSCL_change_status' => ($leadDataBeforeUpdate_LSt_id) ? 'REAL_CHANGE' : 'INITIAL',
                ]
            );
        }
    }

    private function createMatterNameChangeLogTableData($matterDataBeforeUpdate, $createdOrUpdatedMatter)
    {

        $createdOrUpdatedMatter_matter_current_name = $createdOrUpdatedMatter->matter_current_name ?? false;
        $matterDataBeforeUpdate_matter_current_name = $matterDataBeforeUpdate->matter_current_name ?? null;

        if (
            ($createdOrUpdatedMatter_matter_current_name ?? false) &&
            ($createdOrUpdatedMatter_matter_current_name != $matterDataBeforeUpdate_matter_current_name) && ($createdOrUpdatedMatter->matter_id ?? false)
        ) {

            matterNameChangeLog::create(
                [
                    'MNCL_previous_name' => $matterDataBeforeUpdate_matter_current_name,
                    'MNCL_current_name' => $createdOrUpdatedMatter_matter_current_name,
                    'MNCL_action_step_matter_id' => $createdOrUpdatedMatter->matter_id,
                    'MNCL_change_status' => ($matterDataBeforeUpdate_matter_current_name) ? 'REAL_CHANGE' : 'INITIAL',
                ]
            );
        }
    }

    private function createMatterStatusChangeLogTableData($matterDataBeforeUpdate, $createdOrUpdatedMatter)
    {

        $createdOrUpdatedMatter_MSt_id = $createdOrUpdatedMatter->currentMatterStatus->MSt_id ?? false;
        $matterDataBeforeUpdate_MSt_id = $matterDataBeforeUpdate->currentMatterStatus->MSt_id ?? null;

        if (
            ($createdOrUpdatedMatter_MSt_id ?? false) &&
            ($createdOrUpdatedMatter->matter_id ?? false) &&
            ($createdOrUpdatedMatter_MSt_id != $matterDataBeforeUpdate_MSt_id)
        ) {

            matterStatusChangeLog::create(
                [
                    'MSCL_previous_status' => $matterDataBeforeUpdate_MSt_id,
                    'MSCL_current_status' => $createdOrUpdatedMatter_MSt_id,
                    'MSCL_action_step_matter_id' => $createdOrUpdatedMatter->matter_id,
                    'MSCL_change_status' => ($matterDataBeforeUpdate_MSt_id) ? 'REAL_CHANGE' : 'INITIAL',
                ]
            );
        }
    }

    private function createCurrentStepChangeLogTableData($matterDataBeforeUpdate, $createdOrUpdatedMatter)
    {
        $createdOrUpdatedMatter_step_id = $createdOrUpdatedMatter->currentStep->step_id ?? false;
        $matterDataBeforeUpdate_step_id = $matterDataBeforeUpdate->currentStep->step_id ?? null;

        logger(print_r("Matter previous step: " . $matterDataBeforeUpdate_step_id, true));

        if (
            ($createdOrUpdatedMatter_step_id ?? false) &&
            ($createdOrUpdatedMatter->matter_id ?? false) &&
            ($createdOrUpdatedMatter_step_id != $matterDataBeforeUpdate_step_id)
        ) {

            currentStepChangeLog::create(
                [
                    'CSCL_previous_step' => $matterDataBeforeUpdate_step_id,
                    'CSCL_current_step' => $createdOrUpdatedMatter_step_id,
                    'CSCL_action_step_matter_id' => $createdOrUpdatedMatter->matter_id,
                    'CSCL_change_status' => ($matterDataBeforeUpdate_step_id ?? false) ? 'REAL_CHANGE' : 'INITIAL',
                ]
            );
        }
    }

    private function createMatterTypeChangeLogTableData($matterDataBeforeUpdate, $createdOrUpdatedMatter)
    {
        $createdOrUpdatedMatter_MT_id = $createdOrUpdatedMatter->currentMatterType->MT_id ?? false;
        $matterDataBeforeUpdate_MT_id = $matterDataBeforeUpdate->currentMatterType->MT_id ?? null;

        if (
            ($createdOrUpdatedMatter_MT_id ?? false) &&
            ($createdOrUpdatedMatter_MT_id != $matterDataBeforeUpdate_MT_id) && ($createdOrUpdatedMatter->matter_id ?? false)
        ) {

            matterTypeChangeLog::create(
                [
                    'MTCL_previous_matter_type' => $matterDataBeforeUpdate_MT_id,
                    'MTCL_current_matter_type' => $createdOrUpdatedMatter_MT_id,
                    'MTCL_action_step_matter' => $createdOrUpdatedMatter->matter_id,
                    'MTCL_change_status' => ($matterDataBeforeUpdate_MT_id) ? 'REAL_CHANGE' : 'INITIAL',
                ]
            );
        }
    }

    private function createMaterConsultationTableDataAndProcessChangeIfAny($matterDataBeforeUpdate, $createdOrUpdatedMatter, $record)
    {

        if (
            ($createdOrUpdatedMatter->matter_id ?? false) &&
            (($record['Consultation Scheduled'] ?? false) || ($record['Consult Date Conducted'] ?? false))
        ) {
            $intakeData = array();

            $createdOrUpdatedCSP = $this->createOrUpdateIndependentTableData('CSP_platform', $record['Consultation Scheduled By'], new consultationSchedulePlatform()) ?? false;


            if (($createdOrUpdatedCSP->CSP_id ?? false)) $intakeData['CCL_schedule_platform'] = $createdOrUpdatedCSP->CSP_id;

            $intakeData['CCL_action_step_matter_id'] = $createdOrUpdatedMatter->matter_id;
            $intakeData['CCL_outcome'] = $record['Consultation Outcome'];
            $intakeData['CCL_schedule_date'] = ($record['Consultation Scheduled'] ?? false) ? $this->parseCommonDate($record['Consultation Scheduled']) : null;
            $intakeData['CCL_date_conducted'] = ($record['Consult Date Conducted'] ?? false) ? $this->parseCommonDate($record['Consult Date Conducted']) : null;


            $unchangedConsultationLog = consultationChangeLog::where(
                'CCL_action_step_matter_id',
                '=',
                $createdOrUpdatedMatter->matter_id,
            );

            if ($intakeData['CCL_outcome'] ?? false) {
                $unchangedConsultationLog->where('CCL_outcome', '=', $intakeData['CCL_outcome']);
            }
            if ($intakeData['CCL_schedule_date'] ?? false) {
                $unchangedConsultationLog->where('CCL_schedule_date', '=', $intakeData['CCL_schedule_date']);
            }
            if ($intakeData['CCL_date_conducted'] ?? false) {
                $unchangedConsultationLog->where('CCL_date_conducted', '=', $intakeData['CCL_date_conducted']);
            }

            $unchangedConsultationLog = $unchangedConsultationLog->first() ?? false;

            if (!$unchangedConsultationLog) {
                consultationChangeLog::create(
                    $intakeData
                );
            }
        }
    }

    private function createMaterIntakeTableDataAndProcessChangeIfAny($matterDataBeforeUpdate, $createdOrUpdatedMatter, $record)
    {
        $createdOrUpdated_I_S = $this->createOrUpdateIndependentTableData('IS_status', $record['Intake Status'], new intakeStatus()) ?? false;
        $createdOrUpdated_I_D = $this->createOrUpdateIndependentTableData('ID_deposition', $record['Intake Disposition'], new intakeDeposition()) ?? false;
        $createdOrUpdated_I_P = $this->createOrUpdateIndependentTableData('ISP_platform', $record['Intake Scheduled By'], new intakePlatforms()) ?? false;

        if (($createdOrUpdatedMatter->matter_id ?? false) && (($record['Intake Scheduled'] ?? false) || ($record['Intake Completed'] ?? false))) {
            $intakeData = array();

            if (($createdOrUpdated_I_S->IS_id ?? false)) $intakeData['ICL_status'] = $createdOrUpdated_I_S->IS_id;
            if (($createdOrUpdated_I_D->ID_id ?? false)) $intakeData['ICL_deposition'] = $createdOrUpdated_I_D->ID_id;
            if (($createdOrUpdated_I_P->ISP_id ?? false)) $intakeData['ICL_schedule_platform'] = $createdOrUpdated_I_P->ISP_id;
            if (($createdOrUpdatedMatter->currentMatterAttorney->ASALA_id ?? false)) $intakeData['ICL_completed_by'] = $createdOrUpdatedMatter->currentMatterAttorney->ASALA_id;

            $intakeData['ICL_intake_code'] = $record['Intake Code'];
            $intakeData['ICL_action_step_matter_id'] = $createdOrUpdatedMatter->matter_id;
            $intakeData['ICL_schedule_date'] = ($record['Intake Scheduled'] ?? false) ? $this->parseCommonDate($record['Intake Scheduled']) : null;
            $intakeData['ICL_completed_date'] = ($record['Intake Completed'] ?? false) ? $this->parseCommonDate($record['Intake Completed']) : null;


            $unchangedIntakeLog = intakeChangeLog::where(
                'ICL_action_step_matter_id',
                '=',
                $createdOrUpdatedMatter->matter_id,
            );

            if ($intakeData['ICL_status'] ?? false) {
                $unchangedIntakeLog->where('ICL_status', '=', $intakeData['ICL_status']);
            }
            if ($intakeData['ICL_deposition'] ?? false) {
                $unchangedIntakeLog->where('ICL_deposition', '=', $intakeData['ICL_deposition']);
            }
            if ($intakeData['ICL_schedule_platform'] ?? false) {
                $unchangedIntakeLog->where('ICL_schedule_platform', '=', $intakeData['ICL_schedule_platform']);
            }
            if ($intakeData['ICL_completed_by'] ?? false) {
                $unchangedIntakeLog->where('ICL_completed_by', '=', $intakeData['ICL_completed_by']);
            }
            if ($intakeData['ICL_intake_code'] ?? false) {
                $unchangedIntakeLog->where('ICL_intake_code', '=', $intakeData['ICL_intake_code']);
            }
            if ($intakeData['ICL_schedule_date'] ?? false) {
                $unchangedIntakeLog->where('ICL_schedule_date', '=', $intakeData['ICL_schedule_date']);
            }
            if ($intakeData['ICL_completed_date'] ?? false) {
                $unchangedIntakeLog->where('ICL_completed_date', '=', $intakeData['ICL_completed_date']);
            }

            $unchangedIntakeLog = $unchangedIntakeLog->first() ?? false;

            if (!$unchangedIntakeLog) {
                intakeChangeLog::create(
                    $intakeData
                );
            }
        }
    }

    public function storeMattersForFutureProcessing(Request $request): Response
    {
        try {
            //code...
            $request->validate([
                'file' => 'required|mimes:csv',
                'auth' => 'required|string',
            ]);

            $seed = uniqid('', true);
            $seed = hash('md2', $seed);

            // logger('here');

            $csv_temp_name = $seed . '_CSV_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('pending_matters_CSVs', $csv_temp_name);
            
            $resData = response(json_encode(
                [
                    'status' => "success",
                    "message" => "file stored",
                    "file_name" => $csv_temp_name,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r($th->getMessage(), true));
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "message" => $th->getMessage()
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function process(Request $request): Response
    {
        try {
            //code...
            $request->validate([
                'file' => 'required|mimes:csv',
                'auth' => 'required|string',
            ]);

            $seed = uniqid('', true);
            $seed = hash('md2', $seed);

            // logger('here');

            $csv_temp_name = $seed . '_CSV_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('matters_CSVs', $csv_temp_name);
            
            $path = storage_path('app/matters_CSVs') . '/' . $csv_temp_name;

            // Remove first line which content the content "sep=,"
            $lines = file($path);
            array_shift($lines);
            file_put_contents($path, implode('', $lines));

            // Process CSV
            $csv = Reader::createFromStream(fopen($path, 'r+'));
            $csv->setHeaderOffset(0); //set the CSV header offset

            $stmt = Statement::create();
            $records = $stmt->process($csv);

            foreach ($records as $record) {
                try {
                    //code...
                    $this->matterDataBeforeUpdate = matter::where('matter_actionstep_id', '=', $record['ID'])->first() ?? false;
                    $this->leadDataBeforeUpdate = lead::where('lead_email', '=', $record['Primary Participant Email'])->first() ?? false;


                    $this->createdOrUpdatedLead = $this->createOrUpdateLeadByLeadEmail($record);
                    $this->createdOrUpdatedMatter = $this->createOrUpdateMatterByActionStepMatterID($record);

                    $this->linkLeadToMatter($this->createdOrUpdatedMatter, $this->createdOrUpdatedLead);

                    $this->createMaterIntakeTableDataAndProcessChangeIfAny($this->matterDataBeforeUpdate, $this->createdOrUpdatedMatter, $record);
                    $this->createMaterConsultationTableDataAndProcessChangeIfAny($this->matterDataBeforeUpdate, $this->createdOrUpdatedMatter, $record);

                    if (!($this->createdOrUpdatedMatter ?? false)) {
                        continue;
                    }

                    $this->createCurrentStepChangeLogTableData($this->matterDataBeforeUpdate, $this->createdOrUpdatedMatter);
                    $this->createMatterTypeChangeLogTableData($this->matterDataBeforeUpdate, $this->createdOrUpdatedMatter);
                    $this->createMatterStatusChangeLogTableData($this->matterDataBeforeUpdate, $this->createdOrUpdatedMatter);
                    $this->createMatterNameChangeLogTableData($this->matterDataBeforeUpdate, $this->createdOrUpdatedMatter);

                    if (($this->createdOrUpdatedLead ?? false)) {
                        $this->createLeadStatusChangeLogTableData($this->leadDataBeforeUpdate, $this->createdOrUpdatedLead, $this->createdOrUpdatedMatter);
                        $this->createLeadOverallStatusChangeLogTableData($this->leadDataBeforeUpdate, $this->createdOrUpdatedLead, $this->createdOrUpdatedMatter);
                    }
                } catch (\Throwable $th) {
                    logger(print_r($th->getMessage(), true));
                    continue;
                    //throw $th;

                }
            }

            $resData = response(json_encode(
                [
                    'status' => "success",
                    "message" => "success",
                    "file_name" => $csv_temp_name,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r($th->getMessage(), true));
        }
    }

    private function createCurrentStepChangeLogTableDataFromStepsCSV($matter, $step_name, $days)
    {
        if (
            !$matter  ||
            !$step_name  ||
            !($matter->matter_date_created ?? false) ||
            !($matter->matter_id ?? false) ||
            !$days
        ) {
            return;
        }

        $data = [
            "step_name" => $step_name
        ];

        $step = AS_Step::updateOrCreate(
            $data,
            $data,
        ) ?? false;

        if (!$step) {
            return;
        }

        $matter_date_created = $matter->matter_date_created;
        $matter_id = $matter->matter_id;

        $stepChangeDate = $this->addDaysToDate($matter_date_created, $days);

        if (!($stepChangeDate ?? false)) {
            return;
        }

        $lastStepChange = currentStepChangeLog::where(
            "CSCL_action_step_matter_id",
            "=",
            $matter_id
        )->get()->last() ?? false;

        if (
            $lastStepChange &&
            $lastStepChange->CSCL_current_step === $step->step_id && $lastStepChange->CSCL_change_status !==  "REAL_CHANGE"
        ) {
            $lastStepChange->CSCL_change_status =  "REAL_CHANGE";
            $lastStepChange->CSCL_change_date_time  = $stepChangeDate;

            $lastStepChange = $lastStepChange->save();
        } else if (
            (
                ($lastStepChange && $lastStepChange->CSCL_current_step !== $step->step_id) ||
                !$lastStepChange)
        ) {
            $newStatusChange = new currentStepChangeLog();

            $newStatusChange->CSCL_previous_step = $lastStepChange->CSCL_current_step;
            $newStatusChange->CSCL_current_step = $step->step_id;
            $newStatusChange->CSCL_action_step_matter_id = $matter_id;
            $newStatusChange->CSCL_change_status =  "REAL_CHANGE";
            $newStatusChange->CSCL_change_date_time  = $stepChangeDate;

            $newStatusChange = $newStatusChange->save();
        }
    }

    private function createLeadStatusChangeLogTableDataFromStepsCSV($lead, $LSt_status, $days, $matter)
    {

        if (
            !$lead  ||
            !$LSt_status  ||
            !($lead->lead_date_created ?? false) ||
            !($lead->lead_id ?? false) ||
            !($matter->matter_id ?? false) ||
            !$days
        ) {
            return;
        }

        $data = [
            "LSt_status" => $LSt_status
        ];

        $step = leadStatus::updateOrCreate(
            $data,
            $data,
        ) ?? false;

        if (!$step) {
            return;
        }

        $lead_date_created = $lead->lead_date_created;
        $lead_id = $lead->lead_id;

        $statusChangeDate = $this->addDaysToDate($lead_date_created, $days);

        if (!$statusChangeDate) {
            return;
        }

        $lastStatusChange = leadStatusChangeLog::where(
            "LSCL_action_step_lead_id",
            "=",
            $lead_id
        )->get()->last() ?? false;


        if (
            $lastStatusChange &&
            $lastStatusChange->LSCL_current_status === $step->LSt_id &&
            $lastStatusChange->LSCL_change_status !==  "REAL_CHANGE"
        ) {
            $lastStatusChange->LSCL_change_status =  "REAL_CHANGE";
            $lastStatusChange->LSCL_change_date_time  = $statusChangeDate;

            $lastStatusChange = $lastStatusChange->save();
        } else if (
            (
                ($lastStatusChange->LSCL_current_status !== $step->LSt_id) ||
                !$lastStatusChange)
        ) {

            $newStatusChange = new leadStatusChangeLog();

            $newStatusChange->LSCL_previous_status = ($lastStatusChange->LSCL_current_status ?? null);
            $newStatusChange->LSCL_current_status = $step->LSt_id;
            $newStatusChange->LSCL_action_step_lead_id = $lead_id;
            $newStatusChange->LSCL_action_step_matter_id = $matter->matter_id;
            $newStatusChange->LSCL_change_status =  "REAL_CHANGE";
            $newStatusChange->LSCL_change_date_time  = $statusChangeDate;

            $newStatusChange = $newStatusChange->save();
        }
    }

    public function storeStepsForFutureProcessing(Request $request): Response
    {
        try {
            //code...
            $request->validate([
                'file' => 'required|mimes:csv',
                'auth' => 'required|string',
            ]);

            $seed = uniqid('', true);
            $seed = hash('md2', $seed);

            $csv_temp_name = $seed . '_CSV_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('pending_steps_CSVs', $csv_temp_name);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    "message" => "file saved",
                    "file_name" => $csv_temp_name,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r($th->getMessage(), true));
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "message" => $th->getMessage()
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function process_steps(Request $request): Response
    {
        try {
            //code...
            $request->validate([
                'file' => 'required|mimes:csv',
                'auth' => 'required|string',
            ]);

            $seed = uniqid('', true);
            $seed = hash('md2', $seed);

            $csv_temp_name = $seed . '_CSV_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('steps_CSVs', $csv_temp_name);

            $path = storage_path('app/steps_CSVs') . '/' . $csv_temp_name;

            // Remove first line which content the content "sep=,"
            $lines = file($path);
            array_shift($lines);
            file_put_contents($path, implode('', $lines));

            // Process CSV
            $csv = Reader::createFromStream(fopen($path, 'r+'));
            $csv->setHeaderOffset(0); //set the CSV header offset

            $stmt = Statement::create();
            $records = $stmt->process($csv);

            foreach ($records as $record) {
                try {

                    $this->matterDataBeforeUpdate = matter::where('matter_actionstep_id', '=', $record["ID"])->first() ?? false;


                    if (!($this->matterDataBeforeUpdate ?? false)) {
                        throw new \Error("No matter found with given action step id");
                    }

                    $leadAttached = $this->matterDataBeforeUpdate->lead ?? false;

                    // logger(print_r($leadAttached, true));

                    if ($leadAttached) {
                        $this->createLeadStatusChangeLogTableDataFromStepsCSV($leadAttached, "Intake Scheduled", $record["days to_Intake_New Lead"],  $this->matterDataBeforeUpdate);

                        $this->createLeadStatusChangeLogTableDataFromStepsCSV($leadAttached, "Consultation Scheduled", $record["days to_Consultation_New Lead"],  $this->matterDataBeforeUpdate);

                        $this->createLeadStatusChangeLogTableDataFromStepsCSV($leadAttached, "Sent Retainer", $record["days to_Retainer Sent_New Lead"],  $this->matterDataBeforeUpdate);

                        $this->createLeadStatusChangeLogTableDataFromStepsCSV($leadAttached, "Closed - Retained", $record["days to_Closed (Retained)_New Lead"],  $this->matterDataBeforeUpdate);
                    }

                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Intake", $record["days to_Intake_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Consultation", $record["days to_Consultation_New Lead"]);

                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Retainer Sent", $record["days to_Retainer Sent_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Closed (Not Retained)", $record["days to_Closed (Not Retained)_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Questionnaire", $record["days to_Questionnaire_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Retained (Lead Converted)", $record["days to_Retained (Lead Converted)_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Under Review (Atty Review Needed)", $record["days to_Under Review (Atty Review Needed)_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "On Hold (Clt Action Needed)", $record["days to_On Hold (Clt Action Needed)_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Review for Closure", $record["days to_Review for Closure_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "DO NOT USE (Retained)", $record["days to_DO NOT USE (Retained)_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Closed-Not Retained", $record["days to_Closed-Not Retained_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Closed (Retained)", $record["days to_Closed (Retained)_New Lead"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Closed Pre Intake (Not Retained)", $record["days to_Closed Pre Intake (Not Retained)_New Lead"]);

                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Start", $record["days to_Start_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Closed - Resolved/Settled", $record["days to_Closed - Resolved/Settled_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Prep", $record["days to_Prep_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Predemand", $record["days to_Predemand_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Demand", $record["days to_Demand_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Negotiate", $record["days to_Negotiate_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Pre-settled", $record["days to_Pre-settled_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Settled", $record["days to_Settled_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Disbursement", $record["days to_Disbursement_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Severance Negotiation", $record["days to_Severance Negotiation_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Stalled/ On Hold", $record["days to_Stalled/ On Hold_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Admin Remedies (No Neg.)", $record["days to_Admin Remedies (No Neg.)_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "ADR", $record["days to_ADR_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Severance Review (No Negotiation)", $record["days to_Severance Review (No Negotiation)_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Advise & Counsel", $record["days to_Advise & Counsel_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Closed w/out Resolution", $record["days to_Closed w/out Resolution_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Finalize and Close", $record["days to_Finalize and Close_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "DNU Administrative Remedy", $record["days to_DNU Administrative Remedy_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Pre-Negotiation", $record["days to_Pre-Negotiation_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Admin. Remedies (w/Negotiation.)", $record["days to_Admin. Remedies (w/Negotiation.)_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Litigation", $record["days to_Litigation_Prelit"]);
                    $this->createCurrentStepChangeLogTableDataFromStepsCSV($this->matterDataBeforeUpdate, "Presettled", $record["days to_Presettled_Prelit"]);
                } catch (\Throwable $th) {
                    logger(print_r("Processing steps CSV: " . $th->getMessage(), true));
                    continue;
                    //throw $th;

                }
            }

            $resData = response(json_encode(
                [
                    'status' => "success",
                    "message" => "success",
                    "file_name" => $csv_temp_name,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r($th->getMessage(), true));
        }
    }
}
