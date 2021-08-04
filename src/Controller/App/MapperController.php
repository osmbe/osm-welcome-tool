<?php

namespace App\Controller\App;

use App\Entity\Changeset;
use App\Entity\Mapper;
use App\Service\RegionsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapperController extends AbstractController
{
    public function __construct(
        private RegionsProvider $provider,
    ) {}

    #[Route('/{regionKey}/mapper/{id}', name: 'app_mapper')]
    public function index(string $regionKey, int $id): Response
    {
        $region = $this->provider->getRegion($regionKey);

        /** @var Mapper */
        $mapper = $this->getDoctrine()
            ->getRepository(Mapper::class)
            ->find($id);

        return $this->render('app/mapper/index.html.twig', [
            'region' => $region,
            'mapper' => $mapper,
            'changesets' => $mapper->getChangesets(),
        ]);
    }
}
