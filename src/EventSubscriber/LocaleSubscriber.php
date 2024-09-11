<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @param string[] $locales
     *
     * @return void
     */
    public function __construct(
        private readonly string $defaultLocale,
        private readonly array $locales,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // if (!$request->hasPreviousSession()) {
        //     return;
        // }

        if (true === $request->attributes->has('_locale')) {
            // try to see if the locale has been set as a _locale routing parameter
            $locale = $request->attributes->get('_locale');
            $request->getSession()->set('_locale', $locale);
        } elseif (true === $request->query->has('l')) {
            // check if locale is set using `l` query parameter
            $locale = $request->query->get('l');
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } elseif (true === $request->getSession()->has('_locale')) {
            // if no explicit locale has been set on this request, use one from the session
            $locale = $request->getSession()->get('_locale');
            $request->setLocale($locale);
        } elseif (\count($request->getLanguages()) > 0) {
            // if we still don't have a locale defined, use the browser languages
            $languages = $request->getLanguages();
            foreach ($languages as $lang) {
                if (\in_array($lang, $this->locales, true)) {
                    $request->setLocale($lang);
                    break;
                }
            }
        } else {
            // or use the default locale
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
