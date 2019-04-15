<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;

class RefererListener
{
    const LAST_KNOWN_REFERER = 'last_known_referer';

    /**
     * @var SessionInterface
     */
    protected $session;

    protected $domainPatterns = [];

    public function __construct(SessionInterface $session, $domainPatterns)
    {
        $this->session = $session;
        $this->domainPatterns = $domainPatterns;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Leave sub requests alone.
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $referer = $request->headers->get('referer') ?: $request->get('referer');
        $refererDomain = null;

        if ($referer) {
            foreach ($this->domainPatterns as $domain => $patterns) {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $referer)) {
                        $refererDomain = $domain;
                        break 2;
                    }
                }
            }
        }

        if ($refererDomain) {
            $this->session->set(self::LAST_KNOWN_REFERER, $refererDomain);
        }
    }
}
