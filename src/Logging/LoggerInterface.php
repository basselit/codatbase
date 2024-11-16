<?php

namespace Codatsoft\Codatbase\Logging;

use Sentry\EventHint;

interface LoggerInterface
{
    public function logException(\Throwable $exception, EventHint $eventHint = null): void;

}