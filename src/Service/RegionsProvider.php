<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class RegionsProvider
{
    private array $regions = [];

    public function __construct(private string $projectDirectory)
    {
        $yaml = Yaml::parseFile(sprintf('%s/config/regions.yaml', $this->projectDirectory));

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
        $path = sprintf('%s/assets/regions/%s.geojson', $this->projectDirectory, $key);
        if (!file_exists($path) || !is_readable($path)) {
            return null;
        }

        return file_get_contents($path);
    }
}
