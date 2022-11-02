<?php

declare(strict_types=1);

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing;
use Psr\Log\LoggerInterface;
use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator;

/** @var Routing\Router $router */
$router = resolve(Routing\Router::class);

$router->get('sitemap.xml', static function (ResponseFactory $responseFactory): mixed {
    try {
        /** @var SitemapGenerator $generator */
        $generator = resolve(SitemapGenerator::class);
        $generator->generate();
        $generator->output();
    } catch (Throwable $throwable) {
        /** @var LoggerInterface $log */
        $log = resolve(LoggerInterface::class);
        $log->error('Vdlp.Sitemap: Unable to serve sitemap.xml: ' . $throwable->getMessage(), [
            'exception' => $throwable,
        ]);

        return $responseFactory->make('', 500);
    }
});
