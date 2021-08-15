<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpenStreetMapController extends AbstractController
{
    #[Route('/connect/openstreetmap', name: 'connect_openstreetmap_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('openstreetmap') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([], []);
    }

    #[Route('/connect/openstreetmap/check', name: 'connect_openstreetmap_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator

        // $client = $clientRegistry->getClient('openstreetmap');

        // try {
        //     // the exact class depends on which provider you're using
        //     $user = $client->fetchUser();

        //     // do something with all this new power!
        //     var_dump($user->toArray()); exit();
        //     // ...
        // } catch (IdentityProviderException $e) {
        //     // something went wrong!
        //     // probably you should return the reason to the user
        //     var_dump($e->getMessage()); exit();
        // }
    }
}
