<?php
namespace Pyncer\Snyppet\Sentry;

use Psr\Http\Server\MiddlewareInterface as PsrMiddlewareInterface;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Snyppet\Snyppet;

use const Pyncer\Snyppet\Sentry\ENABLED as PYNCER_SENTRY_ENABLED;
use const Pyncer\Snyppet\Sentry\DSN as PYNCER_SENTRY_DSN;
use const Pyncer\Snyppet\Sentry\ENVIRONMENT as PYNCER_SENTRY_ENVIRONMENT;

class SentrySnyppet extends Snyppet
{
    /**
     * @inheritdoc
     */
    protected function initializeMiddleware(string $class): PsrMiddlewareInterface|MiddlewareInterface
    {
        if ($class === '\\Pyncer\\Snyppet\\Sentry\\Middleware\\InitializeMiddleware') {
            return new $class(
                enabled: PYNCER_SENTRY_ENABLED,
                dsn: PYNCER_SENTRY_DSN,
                environment: PYNCER_SENTRY_ENVIRONMENT,
            );
        }

        return parent::initializeMiddleware($class);
    }
}
