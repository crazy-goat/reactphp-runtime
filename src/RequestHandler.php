<?php

namespace Runtime\React;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Message\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

class RequestHandler implements RequestHandlerInterface
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $method = $request->getMethod();
            $headers = $request->getHeaders();

            $query = $request->getQueryParams();
            $content = $request->getBody();
            $post = array();
            if (in_array(strtoupper($method), array('POST', 'PUT', 'DELETE', 'PATCH')) &&
                isset($headers['Content-Type']) && (str_starts_with($headers['Content-Type'][0], 'application/x-www-form-urlencoded'))
            ) {
                parse_str($content, $post);
            }
            $sfRequest = new Request(
                $query,
                $post,
                array(),
                array(),
                $request->getUploadedFiles(),
                array(),
                $content
            );
            $sfRequest->setMethod($method);
            $sfRequest->headers->replace($headers);

            $sfRequest->server->set('REQUEST_URI', $request->getUri()->getPath());
            if (isset($headers['Host'])) {
                $sfRequest->server->set('SERVER_NAME', $headers['Host'][0]);
            }
            $sfResponse = $this->kernel->handle($sfRequest);

            $response = new Response($sfResponse->getStatusCode(), $sfResponse->headers->all(), $sfResponse->getContent());
            $this->kernel->terminate($sfRequest, $sfResponse);

            return $response;
        } catch (Throwable $exception) {
            printf("Exception: %s\n, Trace: %s\n", $exception->getMessage(), $exception->getTraceAsString());
            exit(1);
        }
    }
}
