<?php

namespace PaladinBackend\Cache\RedisCache;

interface RedisCacheFactoryInterface
{
    public function create(string $namespace): RedisCacheInterface;
}