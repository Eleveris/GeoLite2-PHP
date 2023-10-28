<?php

namespace geolite2php\Core;

use GuzzleHttp\Client;

class GeoLite
{
    private static GeoLite $instance;

    private Client $client;
    private function __construct()
    {
        $this->client = new Client([
            'base_uri' => $_ENV['GEOLITE_GUZZLE_URI'],
            'http_errors' => false,
            'auth' => [$_ENV['GEOLITE_ACCOUNT'], $_ENV['GEOLITE_KEY']]
        ]);
    }

    private function __clone(): void
    {
    }

    public static function getInstance(): GeoLite
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCountry(string $ip): string
    {
        $response = $this->client->request('GET', "country/$ip");
        return json_decode($response->getBody()->getContents(), true)['country']['iso_code'] ?? "";
    }
}