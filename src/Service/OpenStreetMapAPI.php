<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenStreetMapAPI
{
    public function __construct(
        private HttpClientInterface $osmClient,
        private HttpClientInterface $client
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
            sprintf('changesets.xml?%s', http_build_query(['user' => $id]))
        );

        return $response;
    }

    public function getDeletedUsers(): ResponseInterface
    {
        $response = $this->client->request(
            'GET',
            'https://planet.openstreetmap.org/users_deleted/users_deleted.txt'
        );

        return $response;
    }
}
