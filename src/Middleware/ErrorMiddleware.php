<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class ErrorMiddleware
{
    public function __construct(private bool $debug = false)
    {
    }

    public function __invoke(ServerRequestInterface $request, $next)
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            return $this->createErrorResponse($e);
        }
    }

    private function createErrorResponse(\Throwable $exception): Response
    {
        $statusCode = $exception->getCode() >= 400 && $exception->getCode() < 600 ? $exception->getCode() : 500;

        $errorData = [
            'error' => true,
            'type' => $exception::class,
            'message' => $exception->getMessage(),
            'code' => $exception->getCode() ?: 500,
        ];

        if ($this->debug) {
            $errorData['trace'] = $exception->getTrace();
        }

        return new Response(
            $statusCode,
            ['Content-Type' => 'application/json'],
            json_encode($errorData),
        );
    }
}
