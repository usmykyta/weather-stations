<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeatherFormRequest;
use App\Services\OpenWeatherApiService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function fetch(WeatherFormRequest $request): JsonResponse
    {
        /** @var OpenWeatherApiService $openWeatherService */
        $openWeatherService = app('openWeather');

        return response()
            ->json([
                'success' => true,
                'data' => $openWeatherService->weather(
                    $request->get('lat'),
                    $request->get('lng'),
                )
            ]);
    }

    /**
     * @return JsonResponse
     */
    public function stations(): JsonResponse
    {
        return response()
            ->json([
                'success' => true,
                'stations' => collect(json_decode(file_get_contents(storage_path('stations.json')), true))
                ->map(function (array $station) {
                    $station['weather'] = Cache::get(sprintf('station_%d', $station['id']));

                    return $station;
                })
            ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getWeather(Request $request): JsonResponse
    {
        $stations = $request->get('stations', []);

        return response()
            ->json([
                'success' => true,
                'results' => collect($stations)->map(fn(int $stationId) => Cache::get(sprintf('station_%d', $stationId)))
            ]);
    }
}
