<?php

namespace App\Controller\App;

use App\Service\RegionsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private RegionsProvider $provider,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $regions = $this->provider->getRegions();

        $keys = array_keys($regions);
        foreach ($keys as $key) {
            $regions[$key]['lastUpdate'] = $this->provider->getLastUpdate($key);
        }

        return $this->render('app/home/index.html.twig', [
            'regions' => $regions,
        ]);
    }
}
