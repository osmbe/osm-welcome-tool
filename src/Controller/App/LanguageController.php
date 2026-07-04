<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LanguageController extends AbstractController
{
    #[Route('/language', name: 'app_language')]
    public function index(): Response
    {
        return $this->render('home/language.html.twig', [
            'controller_name' => 'LanguageController',
        ]);
    }
}
