<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class RegionsProvider
{
    private array $regions = [];

    public function __construct()
    {
        $yaml = Yaml::parseFile('config/regions.yaml');

        $this->regions = $yaml['regions'] ?? [];
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function getRegion(string $key): array|null
    {
        return $this->regions[$key] ?? null;
    }

    public function getGeometry(string $key): string|null
    {
        $path = sprintf('assets/regions/%s.geojson', $key);
        if (!file_exists($path) || !is_readable($path)) {
            return null;
        }

        return file_get_contents($path);
    }
}
