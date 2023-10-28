<?php

namespace geolite2php\Core;
use Memcached;

class Cache
{
    private static Cache $instance;

    private Memcached $memcached;

    private int $ttl;
    private function __construct()
    {
        $this->memcached = new Memcached();
        $this->memcached->addServer($_ENV['MEMCACHED_HOST'], $_ENV['MEMCACHED_PORT']);
        $this->ttl = (int) $_ENV['CACHE_TTL'];
    }

    private function __clone(): void
    {
    }

    public static function getInstance(): Cache
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set(string $key, string $value): bool
    {
        return $this->memcached->set($key, $value, $this->ttl);
    }

    public function get(string $key): string
    {
        return $this->memcached->get($key);
    }
}
