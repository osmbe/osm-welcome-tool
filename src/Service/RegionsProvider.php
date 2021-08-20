<?php

namespace App\Service;

use DateTime;
use Exception;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

class RegionsProvider
{
    private array $regions = [];

    public function __construct(
        private AdapterInterface $cache,
        private string $projectDirectory
    ) {
        $yaml = Yaml::parseFile(sprintf('%s/config/regions.yaml', $this->projectDirectory));

        $this->regions = $yaml['regions'] ?? [];
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function getRegion(string $key): array
    {
        if (!isset($this->regions[$key])) {
            throw new Exception(sprintf('Key "%s" is not defined in regions configuration file.', $key));
        }

        $region = $this->regions[$key];
        $region['key'] = $key;

        return $region;
    }

    public function getGeometry(string $key): string
    {
        $path = sprintf('%s/assets/regions/%s.geojson', $this->projectDirectory, $key);
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception(sprintf('Geometry is not defined for region "%s".', $key));
        }

        return file_get_contents($path);
    }

    public function getLastUpdate(string $key): ?DateTime
    {
        $cacheKey = sprintf('last_update.%s', $key);

        if (true !== $this->cache->hasItem($cacheKey)) {
            return null;
        }

        return new DateTime($this->cache->getItem($cacheKey)->get());
    }
}
