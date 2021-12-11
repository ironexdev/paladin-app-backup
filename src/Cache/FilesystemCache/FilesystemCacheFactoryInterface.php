<?php

namespace Paladin\Cache\FilesystemCache;

interface FilesystemCacheFactoryInterface
{
    public function create(string $namespace): FilesystemCacheInterface;
}