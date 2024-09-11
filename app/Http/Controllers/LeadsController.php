<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Calculators\LeadsCalculator;
use App\Calculators\Timeframe;
use DateTime;

class LeadsController extends Controller
{

    public function getSelectedNewLeads(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $leadCalculator = new LeadsCalculator();
            $leadCalculator->loadLeadsWhereDateCreatedIsWithinTimespan($validated['from'], $validated['to']);
            $newLeads = $leadCalculator->getSelectedNewLeads();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $newLeads,
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
    
    public function getSelectedRetainedLeads(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $leadCalculator = new LeadsCalculator();
            $retainedLeads = $leadCalculator->getSelectedRetainedLeads($validated['from'], $validated['to']);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $retainedLeads,
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

    public function getLeadsBySource(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'filter' => 'required|string'
            ]);

            $leadCalculator = new LeadsCalculator();
            $leadCalculator->loadLeadsWhereDateCreatedIsWithinTimespan($validated['from'], $validated['to']);

            $leadsBySteps = $leadCalculator->getLeadsBySource($validated['filter']);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $leadsBySteps,
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

    public function getLeadsByStep(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'filter' => 'required|string'
            ]);

            $leadCalculator = new LeadsCalculator();
            $leadCalculator->loadLeadsWhereDateCreatedIsWithinTimespan($validated['from'], $validated['to']);

            $leadsBySteps = $leadCalculator->getLeadsByStep($validated['filter']);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $leadsBySteps,
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

}
