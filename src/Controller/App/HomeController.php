<?php

namespace App\Controller\App;

use App\Service\RegionsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly RegionsProvider $provider,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $regions = $this->provider->getRegions();

        return $this->render('app/home/continent.html.twig', [
            'regions' => $regions,
        ]);
    }

    #[Route('/{continent}', name: 'app_continent', requirements: ['continent' => 'asia|africa|australia|europe|north-america|south-america'])]
    public function continent(string $continent): Response
    {
        $regions = $this->provider->getRegions();
        $regions = $regions[$continent];

        foreach ($regions as $key => &$region) {
            $region['lastUpdate'] = $this->provider->getEntity($key)?->getLastUpdate();
            $region['count'] = $this->provider->getPercentage($key);
        }

        return $this->render('app/home/region.html.twig', [
            'continent' => $continent,
            'regions' => $regions,
        ]);
    }
}
