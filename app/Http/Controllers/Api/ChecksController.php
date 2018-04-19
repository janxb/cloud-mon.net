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
        return response()->json(['available_checks' => ['server_creation_time', 'api_response_time']]);
    }

    /**
     * @param $check
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($check)
    {
        if (in_array($check, ['server_creation_time', 'api_response_time'])) {
            return response()->json([
                'checks' => Check::where('check', '=', $check)->whereBetween('created_at', [
                    $this->getLast24Hours()['min']->format('Y-m-d H:i:s'),
                    $this->getLast24Hours()['max']->addHour()->format('Y-m-d H:i:s'),
                ])->get()->groupBy(function ($c) {
                    return $c->provider->name;
                }),
            ]);
        } else {
            return response()->json(['error' => "The type you asked isn't valid"]);
        }
    }

    /**
     * @param $check
     *
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

    private function getData($check, $provider, Carbon $min, Carbon $max)
    {
        $data = [];
        $current = $min;
        while ($current < $max) {
            $tmp = Check::where('check', '=', $check)->where('provider_id', '=', $provider->id)->whereLike('created_at', $current->format('Y-m-d H') . '%')->first();
            if ($tmp == null) {
                $tmp->x = $current->format('d.m.Y H:i');
                $tmp->y = null;
            } else {
                $tmp->x = $tmp->created_at->format('d.m.Y H:i');
                $tmp->y = ($tmp->result == 0) ? null : $tmp->result;
            }
            $data[] = $tmp;
            $current = $current->addHour();
        }

        return $data;
    }

    /**
     * @param $check
     *
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
                'data' => $this->getData($check, $provider, $this->getLast24Hours()['min']->format('Y-m-d H:i:s'),
                    $this->getLast24Hours()['max']->addHour()->format('Y-m-d H:i:s'))
                ,
            ];
        }

        return $response;
    }
}
