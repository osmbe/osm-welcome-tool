<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenStreetMapAPI
{
    public function __construct(
        private HttpClientInterface $client,
        private string $url,
        private string $userAgent
    ) {}

    public function getUsers(array $ids): ResponseInterface
    {
        $response = $this->client->request(
            'GET',
            sprintf('users.json?%s', http_build_query(['users' => implode(',', $ids)])),
            [
                'base_uri' => $this->url,
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => $this->userAgent
                ],
            ]
        );

        return $response;
    }

    public function getChangesetsByUser(int $id): ResponseInterface
    {
        $response = $this->client->request(
            'GET',
            sprintf('changesets.xml?%s', http_build_query(['user' => $id])),
            [
                'base_uri' => $this->url,
                'headers' => [
                    // 'Accept' => 'application/json',
                    'User-Agent' => $this->userAgent
                ],
            ]
        );

        return $response;
    }
}
