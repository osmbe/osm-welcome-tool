<?php

namespace App\Controller\App;

use App\Repository\MapperRepository;
use App\Service\RegionsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    public function __construct(
        private readonly RegionsProvider $provider,
        private readonly MapperRepository $repository,
    ) {
    }

    #[Route('/{regionKey}', name: 'app_region', requirements: ['regionKey' => '[\w\-_]+'])]
    public function redirectToList(string $regionKey): Response
    {
        return $this->redirectToRoute('app_list', ['regionKey' => $regionKey]);
    }

    #[Route('/{regionKey}/list/{year}/{month}', name: 'app_list', requirements: ['regionKey' => '[\w\-_]+'])]
    #[Route('/{continent}/{regionKey}/list/{year}/{month}', name: 'app_list_full', requirements: ['continent' => 'asia|africa|australia|europe|north-america|south-america', 'regionKey' => '[\w\-_]+'])]
    public function index(Request $request, string $regionKey, ?string $continent, ?int $year = null, ?int $month = null): Response
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

        $page = $request->query->getInt('page', 1);

        if (null !== $regionEntity) {
            $paginator = $this->repository->findPaginated($regionEntity, $year, $month, $page);

            return $this->render('app/list/index.html.twig', [
                'region' => $region,
                'month' => (new \DateTime())->setDate($year, $month, 1),
                'paginator' => $paginator,
                'limit' => MapperRepository::MAPPERS_PER_PAGE,
                'currentPage' => $page,
                'previousPage' => max(1, $page - 1),
                'nextPage' => min(ceil($paginator->count() / MapperRepository::MAPPERS_PER_PAGE), $page + 1),
            ]);
        } else {
            return $this->render('app/list/index.html.twig', [
                'region' => $region,
                'month' => (new \DateTime())->setDate($year, $month, 1),
                'paginator' => null,
                'limit' => MapperRepository::MAPPERS_PER_PAGE,
                'currentPage' => $page,
                'previousPage' => max(1, $page - 1),
            ]);
        }
    }
}
