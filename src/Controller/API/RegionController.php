<?php

namespace App\Controller\API;

use App\Repository\MapperRepository;
use App\Service\RegionsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    public function __construct(
        private RegionsProvider $provider,
        private MapperRepository $mapperRepository
    ) {
    }

    #[Route('/api/region/{continent}/{regionKey}.{_format}', name: 'api_region', format: 'geojson', requirements: ['_format' => 'geojson'])]
    public function index(string $continent, string $regionKey): Response
    {
        $region = $this->provider->getRegion($continent, $regionKey);
        $geometry = $this->provider->getGeometry($continent, $regionKey);

        return new JsonResponse([
            'type' => 'Feature',
            'id' => $regionKey,
            'properties' => $region,
            'geometry' => $geometry,
        ]);
    }

    #[Route('/api/region/{continent}/{regionKey}/count.{_format}', name: 'api_region_count', format: 'json', requirements: ['_format' => 'json'])]
    public function count(string $continent, string $regionKey): Response
    {
        $mappers = $this->mapperRepository->findBy(['region' => $regionKey]);

        $count = [];
        foreach ($mappers as $mapper) {
            $datetime = $mapper->getFirstChangeset()->getCreatedAt();
            $date = $datetime->format('Y-m');

            if (!isset($count[$date])) {
                $count[$date] = ['total' => 0, 'welcome' => 0];
            }

            ++$count[$date]['total'];

            if (null !== $mapper->getWelcome()) {
                ++$count[$date]['welcome'];
            }
        }
        ksort($count);

        return new JsonResponse($count);
    }
}
