<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Service\RegionsProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RegionsProvider $provider,
    ) {
    }

    #[Route('/{regionKey}', name: 'app_region', requirements: ['regionKey' => '[\w\-_]+'])]
    public function redirectToList(string $regionKey): Response
    {
        return $this->redirectToRoute('app_list', ['regionKey' => $regionKey]);
    }

    #[Route('/{regionKey}/list/{year}/{month}', name: 'app_list', requirements: ['regionKey' => '[\w\-_]+'])]
    #[Route('/{continent}/{regionKey}/list/{year}/{month}', name: 'app_list_full', requirements: ['continent' => 'asia|africa|australia|europe|north-america|south-america', 'regionKey' => '[\w\-_]+'])]
    public function index(string $regionKey, ?string $continent, int $year = null, int $month = null): Response
    {
        $region = $this->provider->getRegion($continent, $regionKey);
        $regionEntity = $this->provider->getEntity($regionKey);
        $region['lastUpdate'] = null === $regionEntity ? null : $regionEntity->getLastUpdate();
        $region['count'] = $this->provider->getPercentage($regionKey);

        if (null === $year && null === $month) {
            $year = (int) date('Y');
            $month = (int) date('m');
        }

        if ($month > 12) {
            $year = $year + 1;
            $month = 1;

            return $this->redirectToRoute('app_list_full', ['continent' => $region['continent'], 'regionKey' => $region['key'], 'year' => $year, 'month' => $month]);
        } elseif ($month < 1) {
            $year = $year - 1;
            $month = 12;

            return $this->redirectToRoute('app_list_full', ['continent' => $region['continent'], 'regionKey' => $region['key'], 'year' => $year, 'month' => $month]);
        }

        $mappers = null === $regionEntity ? [] : $regionEntity->getMappers()->toArray();

        if (\count($mappers) > 0) {
            $firstChangetsetCreatedAt = array_map(fn (Mapper $mapper): ?\DateTimeImmutable => $mapper->getFirstChangeset()->getCreatedAt(), $mappers);
            array_multisort($firstChangetsetCreatedAt, \SORT_DESC, $mappers);

            $month = (new \DateTime())->setDate($year, $month, 1);

            $mappers = array_filter(
                $mappers,
                function (Mapper $mapper) use ($month): bool {
                    /** @var \DateTimeImmutable */
                    $createdAt = $mapper->getFirstChangeset()->getCreatedAt();

                    return $createdAt->format('Ym') === $month->format('Ym');
                }
            );
        }

        return $this->render('app/list/index.html.twig', [
            'region' => $region,
            'mappers' => $mappers,
            'month' => $month,
        ]);
    }
}
