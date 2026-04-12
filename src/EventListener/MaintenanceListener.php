<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 100)]
final class MaintenanceListener
{
    private string $lockFile;

    private const BYPASS_PREFIXES = ['/admin', '/login', '/logout', '/_', '/assets'];

    public function __construct(
        private readonly Environment $twig,
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
    ) {
        $this->lockFile = $projectDir.'/var/maintenance.lock';
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !file_exists($this->lockFile)) {
            return;
        }

        $path = $event->getRequest()->getPathInfo();

        foreach (self::BYPASS_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return;
            }
        }

        $event->setResponse(new Response(
            $this->twig->render('maintenance.html.twig'),
            Response::HTTP_SERVICE_UNAVAILABLE,
            ['Retry-After' => '3600'],
        ));
    }
}
