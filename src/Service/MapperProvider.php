<?php

namespace App\Service;

use App\Entity\Mapper;
use App\Repository\MapperRepository;
use DateTime;

class MapperProvider
{
    public function __construct(
        private MapperRepository $repository
    ) {
    }

    public function fromOSM(array $array): Mapper
    {
        $mapper = $this->repository->find($array['user']['id']);
        if (null === $mapper) {
            $mapper = new Mapper();
            $mapper->setId($array['user']['id']);
            $mapper->setAccountCreated(new DateTime($array['user']['account_created']));
            $mapper->setStatus('new');
            // $mapper->setRegion($region);
        }

        $mapper->setChangesetsCount($array['user']['changesets']['count']);
        $mapper->setDisplayName($array['user']['display_name']);
        $mapper->setImage($array['user']['img']['href'] ?? null);

        return $mapper;
    }
}
