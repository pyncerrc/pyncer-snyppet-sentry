<?php
namespace Pyncer\Snyppet\Sentry;

use Psr\Http\Server\MiddlewareInterface as PsrMiddlewareInterface;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Snyppet\Sentry\Middleware\InitializeLoggerMiddleware;
use Pyncer\Snyppet\Sentry\Middleware\InitializeMiddleware;
use Pyncer\Snyppet\Snyppet as BaseSnyppet;

use const Pyncer\Snyppet\Sentry\ENABLED as PYNCER_SENTRY_ENABLED;
use const Pyncer\Snyppet\Sentry\DSN as PYNCER_SENTRY_DSN;
use const Pyncer\Snyppet\Sentry\ENVIRONMENT as PYNCER_SENTRY_ENVIRONMENT;
use const Pyncer\Snyppet\Sentry\LOGGER_ENABLED as PYNCER_SENTRY_LOGGER_ENABLED;

class Snyppet extends BaseSnyppet
{
    /**
     * @inheritdoc
     */
    protected function forgeMiddleware(string $class): PsrMiddlewareInterface|MiddlewareInterface
    {
        if ($class === InitializeMiddleware::class) {
            return new $class(
                enabled: PYNCER_SENTRY_ENABLED,
                dsn: PYNCER_SENTRY_DSN,
                environment: PYNCER_SENTRY_ENVIRONMENT,
            );
        }

        if ($class === InitializeLoggerMiddleware::class) {
            return new $class(
                enabled: PYNCER_SENTRY_LOGGER_ENABLED,
            );
        }

        return parent::initializeMiddleware($class);
    }
}
