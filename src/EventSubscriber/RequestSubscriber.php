<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @see https://symfony.com/doc/current/security/form_login_setup.html#redirecting-to-the-last-accessed-page-with-targetpathtrait
 */
class RequestSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (
            true !== $event->isMainRequest()
            || true === $request->isXmlHttpRequest()
            || 'connect_openstreetmap_start' === $request->attributes->get('_route')
        ) {
            return;
        }

        $this->saveTargetPath($request->getSession(), 'main', $request->getUri());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }
}
