<?php

namespace Paladin\Cache\FilesystemCache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FilesystemCacheFactory implements FilesystemCacheFactoryInterface
{
    public function __construct(private string $directory)
    {

    }

    public function create(string $namespace): FilesystemCacheInterface
    {
        $adapter = new FilesystemAdapter($namespace, 0, $this->directory);

        return new FilesystemCache($adapter);
    }
}