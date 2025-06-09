<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class StaticFileMiddleware implements MiddlewareInterface
{
    use ReturnNextResponse;

    public function __construct(private string $rootDirectory)
    {
    }

    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface|ResponseInterface
    {
        if ($this->rootDirectory === '' || $request->getUri()->getPath() === '/') {
            return $this->returnResponse($next($request));
        }

        $fileCandidate = $this->sanitizePathInfo($request->getUri()->getPath(), $this->rootDirectory);

        if ($fileCandidate === null) {
            return $this->returnResponse($next($request));
        }

        if (!is_file($fileCandidate) || !is_readable($fileCandidate)) {
            return $this->returnResponse($next($request));
        }

        return new Response(
            StatusCodeInterface::STATUS_OK,
            [
                'Content-Type' => $this->getMimeType($fileCandidate),
                'Content-Length' =>  strval(intval(filesize($fileCandidate))),
            ],
            strval(file_get_contents($fileCandidate)),
        );

    }

    private function sanitizePathInfo(string $pathInfo, string $baseDir): ?string
    {
        $pathInfo = urldecode($pathInfo);
        $pathInfo = trim($pathInfo, '/');
        $pathInfo = preg_replace('/(\/){2,}/', '/', $pathInfo);
        $fullPath = $baseDir . '/' . $pathInfo;
        $realPath = realpath($fullPath);
        $baseDirPath = realpath($baseDir);

        if ($realPath === false || $baseDirPath === false || !str_starts_with($realPath, $baseDirPath)) {
            return null;
        }

        return $realPath;
    }

    private function getMimeType(string $filename): string
    {
        $typeDefault = 'application/octet-stream';

        if (function_exists('mime_content_type')) {
            $type = mime_content_type($filename);

            if (is_string($type)) {
                return $type;
            }
        }

        if (class_exists(FinfoMimeTypeDetector::class)) {
            $detector = new FinfoMimeTypeDetector();

            $type = $detector->detectMimeTypeFromPath($filename);

            if (is_string($type)) {
                return $type;
            }
        }

        return $typeDefault;
    }
}
