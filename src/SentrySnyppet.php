<?php
namespace Pyncer\Snyppet\Sentry;

use Psr\Http\Server\MiddlewareInterface as PsrMiddlewareInterface;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Snyppet\Snyppet;

class SentrySnyppet extends Snyppet
{
    /**
     * @inheritdoc
     */
    protected function initializeMiddleware(string $class): PsrMiddlewareInterface|MiddlewareInterface
    {
        if ($class === '\\Pyncer\\Snyppet\\Sentry\\Middleware\\InitializeMiddleware') {
            return new $class(
                enabled: SENTRY_ENABLED,
                dsn: SENTRY_DSN,
                environment: SENTRY_ENVIRONMENT,
            );
        }

        return parent::initializeMiddleware($class);
    }
}
