<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OSMChaAPI
{
    public function __construct(
        private readonly HttpClientInterface $osmchaClient,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function listAreasOfInterest(string $order_by, ?int $page = null): ResponseInterface
    {
        $validate = $this->validator->validate($order_by, new Choice(['name', 'date']));

        if ($validate->count() > 0) {
            throw new \ErrorException($validate->get(0)->getMessage());
        }

        $response = $this->osmchaClient->request(
            'GET',
            'aoi/',
            [
                'query' => [
                    'order_by' => $order_by,
                    'page' => $page,
                ],
            ]
        );

        return $response;
    }

    /**
     * @param array<string,string> $filters
     */
    public function createAreaOfInterest(string $name, array $filters): ResponseInterface
    {
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

    /**
     * @param array<string,string> $filters
     */
    public function updateAreaOfInterest(string $id, string $name, array $filters): ResponseInterface
    {
        $validate = $this->validator->validate($id, new Uuid());

        if ($validate->count() > 0) {
            throw new \ErrorException($validate->get(0)->getMessage());
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

    public function getAreaOfInterestChangesets(string $id): ResponseInterface
    {
        $validate = $this->validator->validate($id, new Uuid());

        if ($validate->count() > 0) {
            throw new \ErrorException($validate->get(0)->getMessage());
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
}
