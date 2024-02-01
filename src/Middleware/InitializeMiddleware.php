<?php
namespace Pyncer\Snyppet\Sentry\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;

class InitializeMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected bool $enabled = false,
        protected ?string $dsn = null,
        protected string $environment = 'production',
    )
    {}

    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        if (!$this->enabled || $this->dsn === null) {
            return $handler->next($request, $response);
        }

        \Sentry\init([
            'dsn' => $this->dsn,
            'environment' => $this->environment,
            'error_types' => E_ALL
        ]);

        $handler->onError(function($request, $response, $handler, $middlewareClass, $errorHandler) {
            \Sentry\captureException($errorHandler->getException());
        });

        ID::register(ID::sentry());

        $handler->set(ID::sentry(), true);

        return $handler->next($request, $response);
    }
}
