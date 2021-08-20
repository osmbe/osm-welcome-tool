<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenStreetMapAPI
{
    public function __construct(
        private HttpClientInterface $osmClient
    ) {
    }

    public function getUsers(array $ids): ResponseInterface
    {
        $response = $this->osmClient->request(
            'GET',
            sprintf('users.json?%s', http_build_query(['users' => implode(',', $ids)]))
        );

        return $response;
    }

    public function getChangesetsByUser(int $id): ResponseInterface
    {
        $response = $this->osmClient->request(
            'GET',
            sprintf('changesets.xml?%s', http_build_query(['user' => $id])),
        );

        return $response;
    }
}
