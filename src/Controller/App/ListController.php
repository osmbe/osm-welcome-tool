<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class ListController extends AbstractController
{
    #[Route('/list/{regionKey}', name: 'app_list')]
    public function index(string $regionKey): Response
    {
        $yaml = Yaml::parseFile('../config/regions.yaml');
        $region = $yaml['regions'][$regionKey] ?? null;

        if (is_null($region)) {
            throw new Exception(sprintf('Region "%s" is not configured.', $regionKey));
        }

        /** @var Mapper[] */
        $mappers = $this->getDoctrine()
            ->getRepository(Mapper::class)
            ->findBy(['region' => $regionKey]);

        $firstChangetsetCreatedAt = array_map(function (Mapper $mapper) { return $mapper->getChangesets()->first()->getCreatedAt(); }, $mappers);
        array_multisort($firstChangetsetCreatedAt, SORT_DESC, $mappers);

        return $this->render('app/list/index.html.twig', [
            'region' => $region,
            'mappers' => $mappers,
        ]);
    }
}
