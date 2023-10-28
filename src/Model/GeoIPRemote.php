<?php

namespace geolite2php\Model;

use geolite2php\Core\Cache;
use geolite2php\Core\GeoLite;

class GeoIPRemote
{
    public function get(string $ip): array
    {
        $cached = Cache::getInstance()->get('remote.'.$ip);
        if ($cached) {
            return ['ip' => $ip, 'country' => $cached, 'fresh' => false];
        }
        $geoLite = GeoLite::getInstance();
        $country = $geoLite->getCountry($ip);
        Cache::getInstance()->set('remote.'.$ip, $country);
        return ['ip' => $ip, 'country' => $country, 'fresh' => true];
    }
}
