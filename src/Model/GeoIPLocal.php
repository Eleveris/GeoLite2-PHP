<?php

namespace geolite2php\Model;

use GeoIp2\Exception\AddressNotFoundException;
use geolite2php\Core\Cache;
use geolite2php\Core\GeoLite;
use geolite2php\Core\GeoLiteLocal;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoIPLocal
{
    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function get(string $ip): array
    {
        $cached = Cache::getInstance()->get('local.'.$ip);
        if ($cached) {
            return ['ip' => $ip, 'country' => $cached, 'fresh' => false];
        }
        $geoLite = GeoLiteLocal::getInstance();
        $country = $geoLite->getCountry($ip);
        Cache::getInstance()->set('local.'.$ip, $country);
        return ['ip' => $ip, 'country' => $country, 'fresh' => true];
    }
}
