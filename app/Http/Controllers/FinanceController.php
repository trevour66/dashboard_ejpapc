<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Calculators\FinancesCalculator;
use App\Calculators\Timeframe;
use DateTime;

class FinanceController extends Controller
{

    public function getFeesByResponsibleAtty(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'filter' => 'required|string'
            ]);

            $matterFinancesCalculator = new FinancesCalculator();
            $matterFinancesCalculator->loadAnticipatedFunds($validated['from'], $validated['to']);

            $feesByAtty = $matterFinancesCalculator->prepareFeesByResponsibileAtty($validated['filter']);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $feesByAtty,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("getFeesByResponsibleAtty" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "data" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function getAnticipatedFunds(Request $request)
    {
        try {

            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $matterFinancesCalculator = new FinancesCalculator();
            $matterFinancesCalculator->loadAnticipatedFunds($validated['from'], $validated['to']);
            $matterFinancesCalculator->loadAnticipatedFundsThatHasBeenReceived($validated['from'], $validated['to']);

            $matterFinancesCalculator->calculateAllFinancies();

            $anticipatedFunds = $matterFinancesCalculator->getAnticipateFunds();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $anticipatedFunds,
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

    public function getAttyFeesCollected(Request $request)
    {
        try {

            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $matterFinancesCalculator = new FinancesCalculator();
            $matterFinancesCalculator->loadAnticipatedFunds($validated['from'], $validated['to']);
            $matterFinancesCalculator->loadAnticipatedFundsThatHasBeenReceived($validated['from'], $validated['to']);

            $attyFeesCollected = $matterFinancesCalculator->getAttyFeesCollected();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $attyFeesCollected,
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
