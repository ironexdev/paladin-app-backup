<?php

namespace Paladin\Cache\RedisCache;

use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheFactory implements RedisCacheFactoryInterface
{
    public function __construct(private Redis $connection)
    {

    }

    public function create(string $namespace): RedisCacheInterface
    {
        $adapter = new RedisAdapter($this->connection, $namespace);

        return new RedisCache($adapter);
    }
}