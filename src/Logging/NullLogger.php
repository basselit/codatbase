<?php

namespace Codatsoft\Codatbase\Logging;

use Sentry\EventHint;

class NullLogger implements LoggerInterface
{

    public function logException(\Throwable $exception, EventHint $eventHint = null): void
    {

    }
}