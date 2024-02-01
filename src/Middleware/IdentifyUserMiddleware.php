<?php
namespace Pyncer\Snyppet\Sentry\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;

class IdentifyUserMiddleware implements MiddlewareInterface
{
    public function __construct(protected ?string $dsn = null)
    {}

    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        if (!$handler->has(ID::sentry()) || !$handler->get(ID::sentry())) {
            return $handler->next($request, $response);
        }

        $access = $handler->get(ID::ACCESS);
        if (!$access instanceof AuthenticatorInterface) {
            throw new UnexpectedValueException('Invalid access authenticator.');
        }

        if ($access->isUser()) {
            $user = $access->getUser();
            $user = $user->getData();

            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use($request, $user): void {
                $scope->setUser([
                    'id' => $access->getUserId(),
                    'username' => $user['username'] ?? $user['name'],
                    'email' => $user['email'] ?? null,
                    'ip_address' => $request->getServerParams()['REMOTE_ADDR'] ?? null,
                ]);
            });
        } else {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use($request): void {
                $scope->setUser([
                    'id' => 0,
                    'username' => 'Guest',
                    'ip_address' => $request->getServerParams()['REMOTE_ADDR'] ?? null,
                ]);
            });
        }

        return $handler->next($request, $response);
    }
}
