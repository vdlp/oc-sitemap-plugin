<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\EventSubscribers;

use October\Rain\Events\Dispatcher;

/**
 * Interface EventSubscriber
 *
 * @package Vdlp\Sitemap\Classes\EventSubscribers
 */
interface EventSubscriber
{
    /**
     * @param Dispatcher $dispatcher
     */
    public function subscribe(Dispatcher $dispatcher): void;
}
