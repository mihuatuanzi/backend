<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestTransformer
{
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ('json' === $request->getContentTypeFormat()) {
            $content = $request->getContent();
            if (empty($content)) {
                return;
            }
            $request->request->replace($request->toArray());
        }
    }
}
