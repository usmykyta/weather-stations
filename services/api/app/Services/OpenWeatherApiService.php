<?php

namespace App\Services;

use App\Enums\UserAgentEnum;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class OpenWeatherApiService
{
    private Collection $config;
    private Client $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->loadConfig();
        $this->makeClient();
    }

    private function loadConfig(): void
    {
        $this->config = collect(config('services.open_weather_api'));
    }

    /**
     * @throws Exception
     */
    private function makeClient(): void
    {
        if ($this->config->isEmpty()) {
            throw new Exception('You have to setup an OpenWeather API settings into an .env file before proceeding with this API');
        }

        $this->client = new Client([
            'base_uri' => $this->config->get('url'),
            'headers' => $this->config->get('headers', ['user-agent' => UserAgentEnum::LINUX_CHROME->value()]),
            'handler' => $this->apiKeyHandler(),
            'verify' => app()->environment('production')
        ]);
    }

    /**
     * @return HandlerStack
     */
    private function apiKeyHandler(): HandlerStack
    {
        $handler = HandlerStack::create();

        if ($apiKey = $this->config->get('api_key')) {
            $handler->push(Middleware::mapRequest(function (RequestInterface $request) use ($apiKey) {
                $uri = $request->getUri();
                $uri .= (mb_strpos($uri, '?') ? '&' : '?') . http_build_query(['appId' => $apiKey]);

                return new Request(
                    $request->getMethod(),
                    $uri,
                    $request->getHeaders(),
                    $request->getBody(),
                    $request->getProtocolVersion()
                );
            }));
        }

        return $handler;
    }

    /**
     * @param string $lat
     * @param string $lon
     * @return mixed
     * @throws GuzzleException
     */
    public function weather(string $lat, string $lon, string $units = 'metric'): mixed
    {
        return $this->parseResponse(
            $this->client->get(
                'data/2.5/weather', [
                    'query' => compact(
                        'lat', 'lon', 'units'
                    )
                ]
            )
        );
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    private function parseResponse(ResponseInterface $response): mixed
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
