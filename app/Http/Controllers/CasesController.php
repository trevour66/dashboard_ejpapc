<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Calculators\CaseCalculator;
use App\Calculators\Timeframe;
use DateTime;

class CasesController extends Controller
{

    public function getOpenCases(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $matterCasesCalculator = new CaseCalculator();
            $matterCasesCalculator->loadCases($validated['from'], $validated['to']);
            $matterCasesCalculator->loadOpenCases_query($validated['from'], $validated['to']);

            $openCases = $matterCasesCalculator->getOpenCases();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $openCases,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("Case Controller Error" . $th->getMessage());
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

    public function getCasesByStep(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'filter' => 'required|string'
            ]);

            $matterCasesCalculator = new CaseCalculator();
            $matterCasesCalculator->loadCases($validated['from'], $validated['to']);
            $matterCasesCalculator->loadOpenCases_query($validated['from'], $validated['to']);

            $casesInGivenStep = $matterCasesCalculator->getCasesByStep($validated['filter']);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $casesInGivenStep,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("Case Controller Error" . $th->getMessage());
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

    public function getCasesByAtty(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
                'filter' => 'required|string'
            ]);

            $matterCasesCalculator = new CaseCalculator();
            $matterCasesCalculator->loadCases($validated['from'], $validated['to']);
            $matterCasesCalculator->loadOpenCases_query($validated['from'], $validated['to']);

            $casesByAtty = $matterCasesCalculator->prepareCasesByResponsibileAtty($validated['filter']);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $casesByAtty,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("Case Controller Error" . $th->getMessage());
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

    public function getCaseAccessedWithin10Days(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $matterCasesCalculator = new CaseCalculator();
            $matterCasesCalculator->loadCases($validated['from'], $validated['to']);
            $matterCasesCalculator->loadOpenCases_query($validated['from'], $validated['to']);

            $caseAccessedWithin10Days = $matterCasesCalculator->prepareCasesWhereLastAccessedDateIsGreaterThan10Days();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $caseAccessedWithin10Days,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("Case Controller Error" . $th->getMessage());
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

    public function getCasesWithNextStepOverDue(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);

            $matterCasesCalculator = new CaseCalculator();
            $matterCasesCalculator->loadCases($validated['from'], $validated['to']);
            $matterCasesCalculator->loadOpenCases_query($validated['from'], $validated['to']);

            $casesWithNextStepOverDue = $matterCasesCalculator->prepareCasesWhereNextStepIsOverDue();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => $casesWithNextStepOverDue,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("Case Controller Error" . $th->getMessage());
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
