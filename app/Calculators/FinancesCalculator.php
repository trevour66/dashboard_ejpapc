<?php

namespace App\Calculators;

use App\Calculators\Calculator;

use App\Models\matter;
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

class FinancesCalculator extends Calculator
{
    private ?Collection $loadedAnticipatedFundsCollection  = null;

    private $timeframe;

    public $total_anticipatedFunds = 0;
    public $total_attyFeescollected = 0;
    public $average_attyFeescollected = 0;
    public $average_attyFeescollectedByResponsibeAtty = [];

    private $loadedAnticipatedFundsCollection_query  = null;
    private $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = null;


    public function loadAnticipatedFunds($from = null, $to = null)
    {
        //matter_settlement_funds_expected_date
        try {
            //code...
            $loadedAnticipatedFundsCollection_query = matter::leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->leftJoin('action_step_attorneys_legal_assistants', 'matters.matter_assigned_to', '=', 'action_step_attorneys_legal_assistants.ASALA_id')
                ->leftJoin('a_s__steps', 'matters.matter_current_step', '=', 'a_s__steps.step_id');

            if (
                ($from ?? false)
            ) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);


                    $loadedAnticipatedFundsCollection_query = $loadedAnticipatedFundsCollection_query->where('matters.matter_settlement_funds_expected_date', '>=', $fromDate);
                }
            }


            if (
                ($to ?? false)
            ) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $loadedAnticipatedFundsCollection_query = $loadedAnticipatedFundsCollection_query->where('matters.matter_settlement_funds_expected_date', '<=', $toDate);
                }
            }

            $this->loadedAnticipatedFundsCollection_query = clone $loadedAnticipatedFundsCollection_query;

            $this->loadedAnticipatedFundsCollection = $loadedAnticipatedFundsCollection_query->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->get();

            // logger(print_r("loadAnticipatedFunds " . count($this->loadedAnticipatedFundsCollection), true));
        } catch (Throwable $th) {

            logger(print_r("loadAnticipatedFunds Error " . $th->getMessage(), true));
            $this->loadedAnticipatedFundsCollection = collect([]);
        }
    }

    public function loadAnticipatedFundsThatHasBeenReceived($from = null, $to = null)
    {
        //matter_settlement_funds_expected_date
        try {
            //code...
            $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = matter::leftJoin('leads', 'matters.matter_lead', '=', 'leads.lead_id')
                ->leftJoin('action_step_attorneys_legal_assistants', 'matters.matter_assigned_to', '=', 'action_step_attorneys_legal_assistants.ASALA_id')
                ->leftJoin('a_s__steps', 'matters.matter_current_step', '=', 'a_s__steps.step_id');

            if (
                ($from ?? false)
            ) {
                if ($from !== 'all') {
                    $fromDate = new DateTime($from);


                    $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = $loadedAnticipatedFundsThatHasBeenReceivedCollection_query->where('matters.matter_settlement_funds_expected_date', '>=', $fromDate);
                }
            }


            if (
                ($to ?? false)
            ) {
                if ($to !== 'all') {
                    $toDate = new DateTime($to);

                    $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = $loadedAnticipatedFundsThatHasBeenReceivedCollection_query->where('matters.matter_settlement_funds_expected_date', '<=', $toDate);
                }
            }

            $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = $loadedAnticipatedFundsThatHasBeenReceivedCollection_query->where('matters.matter_settlement_funds_received_date', '!=', null);

            $this->loadedAnticipatedFundsThatHasBeenReceivedCollection_query = clone $loadedAnticipatedFundsThatHasBeenReceivedCollection_query;
        } catch (Throwable $th) {

            logger(print_r("loadAnticipatedFunds Error " . $th->getMessage(), true));
            $this->loadedAnticipatedFundsCollection = collect([]);
        }
    }

    public function calculateAllFinancies_fund_received()
    {
        try {
            $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = clone
                $this->loadedAnticipatedFundsThatHasBeenReceivedCollection_query;

            $loadedAnticipatedFundsThatHasBeenReceivedCollection =  $loadedAnticipatedFundsThatHasBeenReceivedCollection_query->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->get() ?? false;

            // logger(print_r("loadedAnticipatedFundsThatHasBeenReceivedCollection", true));
            // logger(print_r($loadedAnticipatedFundsThatHasBeenReceivedCollection, true));
            if (!($loadedAnticipatedFundsThatHasBeenReceivedCollection ?? false)) {
                $this->total_attyFeescollected = 0;
                $this->average_attyFeescollected = 0;
                return;
            }

            for ($i = 0; $i < count($loadedAnticipatedFundsThatHasBeenReceivedCollection); $i++) {

                $elem = $loadedAnticipatedFundsThatHasBeenReceivedCollection[$i];
                $matter_atty_fees = $elem->matter_atty_fees ?? 0;

                $this->total_attyFeescollected = $this->total_attyFeescollected + $matter_atty_fees;
            }

            if ((($this->total_attyFeescollected ?? 0) > 0)) {
                $result = $this->total_attyFeescollected / count($loadedAnticipatedFundsThatHasBeenReceivedCollection);
                $this->average_attyFeescollected = floatval($result);
            }


            // logger(print_r("total_attyFeescollected :" . $this->total_attyFeescollected, true));
            // logger(print_r("average_attyFeescollected :" . $this->average_attyFeescollected, true));
        } catch (Throwable $th) {
            $this->total_attyFeescollected = 0;
            $this->average_attyFeescollected = 0;
            logger("Error : calculateAllFinancies_fund_received" . $th->getMessage());
        }
    }

    public function calculateAllFinancies()
    {
        try {

            if (!($this->loadedAnticipatedFundsCollection ?? false)) {
                $this->total_anticipatedFunds = 0;
                return;
            }

            for ($i = 0; $i < count($this->loadedAnticipatedFundsCollection); $i++) {

                $elem = $this->loadedAnticipatedFundsCollection[$i];

                $responsibeAtty = $elem->ASALA_name ?? 'unallocated';

                $matter_current_offer = $elem->matter_current_offer ?? 0;
                $matter_atty_fees = $elem->matter_atty_fees ?? 0;

                $this->total_anticipatedFunds = $this->total_anticipatedFunds + $matter_current_offer;

                if (isset($this->average_attyFeescollectedByResponsibeAtty[$responsibeAtty])) {
                    $this->average_attyFeescollectedByResponsibeAtty[$responsibeAtty] += $matter_atty_fees;
                } else {
                    $this->average_attyFeescollectedByResponsibeAtty[$responsibeAtty] = $matter_atty_fees;
                }
            }


            // logger(print_r("total_anticipatedFunds :" . $this->total_anticipatedFunds, true));
            // logger(print_r("average_attyFeescollected :" . $this->average_attyFeescollected, true));
            // logger(print_r("average_attyFeescollectedByResponsibeAtty :", true));
            // logger(print_r($this->average_attyFeescollectedByResponsibeAtty, true));
        } catch (Throwable $th) {
            $this->total_anticipatedFunds = "undefined";
            logger("Error : prepareAverageTime_newLead_to_retained" . $th->getMessage());
        }
    }

    public function prepareFeesByResponsibileAtty($atty_name)
    {
        try {

            if (!($this->loadedAnticipatedFundsCollection_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedAnticipatedFundsCollection_query = clone $this->loadedAnticipatedFundsCollection_query;

            $feesByAtty = $loadedAnticipatedFundsCollection_query->where('action_step_attorneys_legal_assistants.ASALA_name', '=', $atty_name)->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->cursorPaginate(20);

            return $feesByAtty;
        } catch (Throwable $th) {
            logger("Error : prepareFeesByResponsibileAtty" . $th->getMessage());
            return [];
        }
    }

    public function getAnticipateFunds()
    {
        try {

            if (!($this->loadedAnticipatedFundsCollection_query ?? false)) {
                throw new Error('Query empty');
            }

            $loadedAnticipatedFundsCollection_query = clone $this->loadedAnticipatedFundsCollection_query;

            $anticipatedFunds = $loadedAnticipatedFundsCollection_query->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->cursorPaginate(20);

            return $anticipatedFunds;
        } catch (Throwable $th) {
            logger("Error : getAnticipateFunds" . $th->getMessage());
            return [];
        }
    }

    public function getAttyFeesCollected()
    {
        try {

            $loadedAnticipatedFundsThatHasBeenReceivedCollection_query = clone
                $this->loadedAnticipatedFundsThatHasBeenReceivedCollection_query;

            $loadedAnticipatedFundsThatHasBeenReceivedCollection =  $loadedAnticipatedFundsThatHasBeenReceivedCollection_query->select(
                [
                    'matters.*',
                    'action_step_attorneys_legal_assistants.*',
                    'leads.*',
                    'a_s__steps.*'
                ]
            )->cursorPaginate(20);

            return $loadedAnticipatedFundsThatHasBeenReceivedCollection;
        } catch (Throwable $th) {
            logger("Error : getAttyFeesCollected" . $th->getMessage());
            return [];
        }
    }
}
