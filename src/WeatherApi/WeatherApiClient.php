<?php

namespace App\WeatherApi;

use App\WeatherApi\Exception\ApiException;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class WeatherApiClient extends \GuzzleHttp\Client
{
    public function __construct(private string $baseUrl, private string $apiKey, private string $lang = 'hu')
    {
        parent::__construct();
    }

    public function getCurrent(string $location): array
    {
        try {
            $response = $this->get(sprintf('%s/v1/current.json?%s', rtrim($this->baseUrl, '/'), $this->buildQueryString($location)));

            return $this->processResponse($response);
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);

            if (isset($response['error'])) {
                throw new ApiException(sprintf('%s (code: %d)', $response['error']['message'], $response['error']['code']), $e->getResponse()->getStatusCode(), $e);
            } else {
                throw new ApiException(sprintf('Client error: %s', $e->getMessage()), $e->getResponse()->getStatusCode());
            }
        }
    }

    private function processResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    private function buildQueryString(string $location): string
    {
        return http_build_query([
            'q' => $location,
            'lang' => $this->lang,
            'key' => $this->apiKey
        ]);
    }
}