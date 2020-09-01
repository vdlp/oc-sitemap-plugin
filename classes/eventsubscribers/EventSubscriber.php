<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\EventSubscribers;

use October\Rain\Events\Dispatcher;

interface EventSubscriber
{
    public function subscribe(Dispatcher $dispatcher): void;
}
