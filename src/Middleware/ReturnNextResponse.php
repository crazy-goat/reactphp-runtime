<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;

trait ReturnNextResponse
{
    /** @return ResponseInterface|PromiseInterface<ResponseInterface> */
    public function returnResponse(mixed $response): ResponseInterface|PromiseInterface
    {
        if ($response instanceof PromiseInterface || $response instanceof ResponseInterface) {
            return $response;
        }

        throw new \RuntimeException('Invalid response, expected PromiseInterface or ResponseInterface');
    }
}
