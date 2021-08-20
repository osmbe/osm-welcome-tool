<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Service\RegionsProvider;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    public function __construct(
        private RegionsProvider $provider,
    ) {
    }

    #[Route('/{regionKey}/list/{year}/{month}', name: 'app_list')]
    public function index(string $regionKey, ?int $year = null, ?int $month = null): Response
    {
        $region = $this->provider->getRegion($regionKey);

        if (is_null($year) && is_null($month)) {
            $year = intval(date('Y'));
            $month = intval(date('m'));
        }

        /** @var Mapper[] */
        $mappers = $this->getDoctrine()
            ->getRepository(Mapper::class)
            ->findBy(['region' => $regionKey]);

        $firstChangetsetCreatedAt = array_map(function (Mapper $mapper) { return $mapper->getFirstChangeset()->getCreatedAt(); }, $mappers);
        array_multisort($firstChangetsetCreatedAt, SORT_DESC, $mappers);

        $month = (new DateTime())->setDate($year, $month, 1);

        $mappers = array_filter(
            $mappers,
            function (Mapper $mapper) use ($month) {
                /** @var DateTimeImmutable */
                $createdAt = $mapper->getFirstChangeset()->getCreatedAt();

                return $createdAt->format('Ym') === $month->format('Ym');
            }
        );

        return $this->render('app/list/index.html.twig', [
            'region'  => $region,
            'mappers' => $mappers,
            'month'   => $month,
        ]);
    }
}
