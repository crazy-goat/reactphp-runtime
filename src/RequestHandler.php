<?php

namespace CrazyGoat\ReactPHPRuntime;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Throwable;

class RequestHandler implements RequestHandlerInterface
{
    private HttpKernelInterface $kernel;
    private HttpFoundationFactory $httpFoundationFactory;
    private PsrHttpFactory $httpMessageFactory;

    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->httpFoundationFactory = new HttpFoundationFactory();
        $this->httpMessageFactory = new PsrHttpFactory();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $sfRequest = $this->httpFoundationFactory->createRequest($request);
            $sfResponse = $this->kernel->handle($sfRequest);
            $response = $this->httpMessageFactory->createResponse($sfResponse);
            if ($this->kernel instanceof TerminableInterface) {
                $this->kernel->terminate($sfRequest, $sfResponse);
            }
            return $response;
        } catch (Throwable $exception) {
            printf("Exception: %s\n, Trace: %s\n", $exception->getMessage(), $exception->getTraceAsString());
            exit(1);
        }
    }
}
