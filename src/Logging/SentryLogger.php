<?php

namespace Codatsoft\Codatbase\Logging;

use Sentry\EventHint;
use Sentry\State\HubInterface;
use Sentry\ClientBuilder;

class SentryLogger implements LoggerInterface
{
    protected $sentry;

    public function __construct(HubInterface $sentry)
    {
        $this->sentry = $sentry;
    }

    public function logException(\Throwable $exception, EventHint $eventHint = null): void
    {
        $this->sentry->captureException($exception, $eventHint);

    }
}