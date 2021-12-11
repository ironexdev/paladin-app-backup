<?php

namespace Paladin\Cache\RedisCache;

interface RedisCacheFactoryInterface
{
    public function create(string $namespace): RedisCacheInterface;
}