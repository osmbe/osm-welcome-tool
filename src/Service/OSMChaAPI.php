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
        private HttpClientInterface $osmchaClient,
        private ValidatorInterface $validator
    ) {}

    public function createAreaOfInterest(string $name, array $filters): ResponseInterface {
        $response = $this->osmchaClient->request(
            'POST',
            'aoi/',
            [
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

        $response = $this->osmchaClient->request(
            'PUT',
            sprintf('aoi/%s/', $id),
            [
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

        $response = $this->osmchaClient->request(
            'GET',
            sprintf('aoi/%s/changesets/', $id),
            [
                'query' => [
                    'page_size' => 500,
                ],
            ]
        );

        return $response;
    }

    public function getChangesets(array $query): ResponseInterface
    {
        $response = $this->osmchaClient->request(
            'GET',
            'changesets/',
            [
                'query' => $query,
            ]
        );

        return $response;
    }
}