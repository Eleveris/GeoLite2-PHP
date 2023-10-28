<?php

namespace geolite2php\Core;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoLiteLocal
{
    private static GeoLiteLocal $instance;

    private Reader $GeoIP2;

    /**
     * @throws InvalidDatabaseException
     */
    private function __construct()
    {
        $this->GeoIP2 = new Reader($_ENV['GEOLITE_DBFILEPATH']);
    }

    private function __clone(): void
    {
    }

    public static function getInstance(): GeoLiteLocal
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCountry(string $ip): string
    {
        return $this->GeoIP2->country($ip)->country->isoCode;
    }
}
