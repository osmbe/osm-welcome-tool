<?php

namespace App\Service;

use App\Entity\Mapper;
use App\Repository\MapperRepository;
use App\Repository\WelcomeRepository;
use DateTime;
use Exception;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

class RegionsProvider
{
    private array $regions = [];

    public function __construct(
        private AdapterInterface $cache,
        private MapperRepository $mapperRepository,
        private WelcomeRepository $welcomeRepository,
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

    public function getGeometry(string $key): array
    {
        $path = sprintf('%s/assets/regions/%s.geojson', $this->projectDirectory, $key);
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception(sprintf('Geometry is not defined for region "%s".', $key));
        }

        $content = file_get_contents($path);
        if (false === $content) {
            throw new Exception(sprintf('Can\'t read geometry for region "%s".', $key));
        }
        $data = json_decode($content, true);
        if (null === $data) {
            throw new Exception(sprintf('Geometry for region "%s" doesn\'t seem to be valid.', $key));
        }

        return $data;
    }

    public function getLastUpdate(string $key): ?DateTime
    {
        $cacheKey = sprintf('last_update.%s', $key);

        if (true !== $this->cache->hasItem($cacheKey)) {
            return null;
        }

        return new DateTime($this->cache->getItem($cacheKey)->get());
    }

    public function getPercentage(string $key): int
    {
        /** @var Mapper[] */
        $mappers = $this->mapperRepository->findBy(['region' => $key]);

        $checked = array_filter($mappers, function (Mapper $mapper): bool {
            return null !== $mapper->getWelcome() || false === $mapper->getNotes()->isEmpty();
        });

        return \count($mappers) > 0 ? round(\count($checked) / \count($mappers) * 100) : 0;
    }
}
