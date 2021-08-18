<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{

    public function __construct(private string $defaultLocale)
    {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // if (!$request->hasPreviousSession()) {
        //     return;
        // }

        // try to see if the locale has been set as a _locale routing parameter
        if ($request->attributes->has('_locale') === true) {
            $locale = $request->attributes->get('_locale');
            $request->getSession()->set('_locale', $locale);
        }
        // check if locale is set using `l` query parameter
        else if ($request->query->has('l') === true) {
            $locale = $request->query->get('l');
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        }
        // if no explicit locale has been set on this request, use one from the session
        else if ($request->getSession()->has('_locale') === true) {
            $locale = $request->getSession()->get('_locale');
            $request->setLocale($locale);
        }
        // if we still don't have a locale defined, use the browser languages
        else if (count($request->getLanguages()) > 0) {
            $languages = $request->getLanguages();
            $request->setLocale($languages[0]);
        }
        // or use the default locale
        else {
            $request->setLocale($this->defaultLocale);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
