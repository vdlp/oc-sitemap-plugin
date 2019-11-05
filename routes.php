<?php

declare(strict_types=1);

use Illuminate\Routing;
use Psr\Log\LoggerInterface;
use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator;

/** @var Routing\Router $router */
$router = resolve(Routing\Router::class);

$router->get('sitemap.xml', static function () {
    try {
        /** @var SitemapGenerator $generator */
        $generator = resolve(SitemapGenerator::class);
        $generator->generate();
        $generator->output();
    } catch (Throwable $e) {
        /** @var LoggerInterface $log */
        $log = resolve(LoggerInterface::class);
        $log->error($e);
    }
});
