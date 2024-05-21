<?php
namespace Pyncer\Snyppet\Sentry\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;

class InitializeMiddleware implements MiddlewareInterface
{
    protected bool $enabled;
    protected ?string $dsn;
    protected string $environment;

    public function __construct(
        bool $enabled = false,
        ?string $dsn = null,
        string $environment = 'production',
    ) {
        $this->setEnabled($enabled);
        $this->setDsn($dsn);
        $this->setEnvironment($environment);
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }
    public function setEnabled(bool $value): static
    {
        $this->enabled = $value;
        return $this;
    }

    public function getDsn(): ?string
    {
        return $this->dsn;
    }
    public function setDsn(?string $value): static
    {
        $this->dsn = $value;
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
    public function setEnvironment(string $value): static
    {
        $this->environment = $value;
        return $this;
    }

    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        if (!$this->getEnabled() || $this->getDsn() === null) {
            return $handler->next($request, $response);
        }

        \Sentry\init([
            'dsn' => $this->getDsn(),
            'environment' => $this->getEnvironment(),
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
