<?php

namespace App\Console\Commands;

use App\Services\OpenWeatherApiService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StationUpdateWeatherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'station:update-weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Weather for a stations';

    /**
     * Execute the console command.
     * @throws GuzzleException
     */
    public function handle(): void
    {
        Log::info('open_weather.api', ['refresh' => true]);

        /** @var OpenWeatherApiService $openWeatherService */
        $openWeatherService = app('openWeather');
        $stations = collect(json_decode(file_get_contents(storage_path('stations.json')), true));

        $stations->each(function (array $station) use ($openWeatherService) {
            $result = $openWeatherService->weather($station['lat'], $station['lng']);

            $weather = current($result['weather']);

            Cache::forever(sprintf('station_%d', $station['id']), [
                'name' => $result['name'],
                'weather' => $weather,
                'wind' => $result['wind'],
                'temperatures' => $result['main'],
                'icon' => sprintf('https://openweathermap.org/img/wn/%s@2x.png', $weather['icon'])
            ]);
        });

        $this->output->success('OK!');
    }
}
