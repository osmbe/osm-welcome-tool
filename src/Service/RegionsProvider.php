<?php

namespace App\Service;

use App\Entity\Mapper;
use App\Repository\MapperRepository;
use App\Repository\RegionRepository;
use App\Repository\WelcomeRepository;
use Symfony\Component\Yaml\Yaml;

class RegionsProvider
{
    private array $regions = [];

    public function __construct(
        private readonly RegionRepository $regionRepository,
        private readonly MapperRepository $mapperRepository,
        private readonly WelcomeRepository $welcomeRepository,
        private readonly string $projectDirectory
    ) {
        $yaml = Yaml::parseFile(sprintf('%s/config/regions.yaml', $this->projectDirectory));

        $this->regions = $yaml['regions'] ?? [];
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function getRegion(?string $continent, string $key): array
    {
        if (null !== $continent && !isset($this->regions[$continent][$key])) {
            throw new \Exception(sprintf('Key "%s.%s" is not defined in regions configuration file.', $continent, $key));
        }

        if (null === $continent) {
            $group = array_filter($this->regions, fn ($value) => \in_array($key, array_keys($value), true));

            if (0 === \count($group)) {
                throw new \Exception(sprintf('Key "%s" is not defined in regions configuration file.', $key));
            } else {
                $continent = array_key_first($group);
            }
        }

        $region = $this->regions[$continent][$key];
        $region['key'] = $key;
        $region['continent'] = $continent;

        return $region;
    }

    public function getGeometry(string $continent, string $key): array
    {
        $path = sprintf('%s/assets/regions/%s/%s.geojson', $this->projectDirectory, $continent, $key);
        if (!file_exists($path) || !is_readable($path)) {
            throw new \Exception(sprintf('Geometry is not defined for region "%s".', $key));
        }

        $content = file_get_contents($path);
        if (false === $content) {
            throw new \Exception(sprintf('Can\'t read geometry for region "%s".', $key));
        }
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        if (null === $data) {
            throw new \Exception(sprintf('Geometry for region "%s" doesn\'t seem to be valid.', $key));
        }

        return $data;
    }

    public function getLastUpdate(string $key): ?\DateTime
    {
        $region = $this->regionRepository->find($key);

        if (null === $region) {
            return null;
        }

        return $region->getLastUpdate();
    }

    public function getPercentage(string $key): array
    {
        /** @var Mapper[] */
        $mappers = $this->mapperRepository->findBy(['region' => $key]);

        $checked = array_filter($mappers, fn (Mapper $mapper): bool => null !== $mapper->getWelcome() || false === $mapper->getNotes()->isEmpty());

        return [
            'count' => \count($checked),
            'total' => \count($mappers),
            'percentage' => \count($mappers) > 0 ? round(\count($checked) / \count($mappers) * 100) : 0,
        ];
    }
}
