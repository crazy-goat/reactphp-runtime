<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

interface MiddlewareInterface
{
    /**      @return ResponseInterface|PromiseInterface<ResponseInterface> */
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface|PromiseInterface;
}
