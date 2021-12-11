<?php

namespace PaladinBackend\Cache\FilesystemCache;

interface FilesystemCacheFactoryInterface
{
    public function create(string $namespace): FilesystemCacheInterface;
}