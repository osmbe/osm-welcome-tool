<?php

namespace App\Service;

use ErrorException;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OSMChaAPI
{
    public function __construct(
        private HttpClientInterface $client,
        private ValidatorInterface $validator,
        private string $url,
        private string $key
    ) {}

    public function createAreaOfInterest(string $name, array $filters): ResponseInterface {
        $response = $this->client->request(
            'POST',
            'aoi/',
            [
                'base_uri' => $this->url,
                'headers' => [
                    'Accept' => 'application/json',
                    // 'Content-Type' => 'application/json',
                    'Authorization' => $this->key
                ],
                'json' => [
                    'name' => $name,
                    'filters' => $filters,
                ],
            ]
        );

        return $response;
    }

    public function updateAreaOfInterest(string $id, string $name, array $filters): ResponseInterface {
        $validate = $this->validator->validate($id, new Uuid());

        if ($validate->count() > 0) {
            throw new ErrorException($validate->get(0)->getMessage());
        }

        $response = $this->client->request(
            'PUT',
            sprintf('aoi/%s/', $id),
            [
                'base_uri' => $this->url,
                'headers' => [
                    'Accept' => 'application/json',
                    // 'Content-Type' => 'application/json',
                    'Authorization' => $this->key
                ],
                'json' => [
                    'name' => $name,
                    'filters' => $filters,
                ],
            ]
        );

        return $response;
    }

    public function getAreaOfInterestChangesets(string $id): ResponseInterface {
        $validate = $this->validator->validate($id, new Uuid());

        if ($validate->count() > 0) {
            throw new ErrorException($validate->get(0)->getMessage());
        }

        $response = $this->client->request(
            'GET',
            sprintf('aoi/%s/changesets/', $id),
            [
                'base_uri' => $this->url,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $this->key
                ],
                'query' => [
                    'page_size' => 500,
                ],
            ]
        );

        return $response;
    }

    public function getChangesets(array $query): ResponseInterface
    {
        $response = $this->client->request(
            'GET',
            'changesets/',
            [
                'base_uri' => $this->url,
                'headers' => [
                    // 'Content-Type' => 'application/json',
                    'Authorization' => $this->key
                ],
                'query' => $query,
            ]
        );

        return $response;
    }
}