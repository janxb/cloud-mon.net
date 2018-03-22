<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CheckResource;
use App\Models\Check;
use App\Models\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 *
 */
class ChecksController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(['available_checks' => Check::groupBy('check')->pluck('check')]);
    }

    /**
     * @param $check
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($check)
    {
        if (in_array($check, Check::groupBy('check')->pluck('check')->toArray())) {
            return response()->json([
                'checks' => Check::where('check', '=', $check)->get()->groupBy(function ($c) {
                    return $c->provider->name;
                }),
            ]);
        } else {
            return response()->json(['error' => "The type you asked isn't valid"]);
        }
    }

    /**
     * @param $check
     * @return \Illuminate\Http\JsonResponse
     */
    public function checksForCharts($check)
    {
        return response()->json([
            'labels' => $this->getLast24Hours()['labels'],
            'datasets' => $this->getDataSets($check),
        ]);
    }

    /**
     * @return array
     */
    private function getLast24Hours()
    {
        $d = ['labels' => [], 'min' => '', 'max' => ''];
        $_d = Carbon::now()->startOfHour()->addHour()->subDay();
        $d['labels'][] = $_d->format('d.m.Y h\:00 a');
        $d['min'] = Carbon::now()->startOfHour()->addHour()->subDay();
        for ($f = 1; $f < 24; $f++) {
            $d['labels'][] = $_d->startOfHour()->addHour()->format('d.m.Y h\:00 a');
        }
        $d['max'] = Carbon::now()->endOfHour();

        return $d;
    }

    /**
     * @param $check
     * @return array
     */
    private function getDataSets($check)
    {
        $providers = Provider::all();
        $response = [];
        foreach ($providers as $provider) {
            $response[] = [
                'label' => $provider->name,
                'fill' => false,
                'backgroundColor' => $provider->color,
                'borderColor' => $provider->color,
                'data' => Check::where('check', '=', $check)->where('provider_id', '=', $provider->id)->whereBetween('created_at', [
                    $this->getLast24Hours()['min']->format('Y-m-d H:i:s'),
                    $this->getLast24Hours()['max']->addHour()->format('Y-m-d H:i:s'),
                ])->get()->map(function (
                    $c
                ) {
                    $c->x = $c->created_at->format('d.m.Y H:i:s');
                    $c->y = $c->result;

                    return $c;
                }),
            ];
        }

        return $response;
    }
}
