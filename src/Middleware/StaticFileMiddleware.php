<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class StaticFileMiddleware
{
    public function __construct(private string $rootDirectory)
    {
    }

    public function __invoke(ServerRequestInterface $request, $next)
    {
        if ($this->rootDirectory === '' || $request->getUri()->getPath() === '/') {
            return $next($request);
        }

        $fileCandidate = $this->sanitizePathInfo($request->getUri()->getPath(), $this->rootDirectory);

        if ($fileCandidate === null) {
            return $next($request);
        }

        if (!is_file($fileCandidate) || !is_readable($fileCandidate)) {
            return $next($request);
        }
        echo "$fileCandidate" . PHP_EOL;
        return new Response(
            StatusCodeInterface::STATUS_OK,
            [
                'Content-Type' => $this->getMimeType($fileCandidate),
                'Content-Length' => filesize($fileCandidate),
            ],
            file_get_contents($fileCandidate),
        );

    }

    private function sanitizePathInfo(string $pathInfo, string $baseDir): ?string
    {
        $pathInfo = urldecode($pathInfo);
        $pathInfo = trim($pathInfo, '/');
        $pathInfo = preg_replace('/(\/){2,}/', '/', $pathInfo);
        $fullPath = $baseDir . '/' . $pathInfo;
        $realPath = realpath($fullPath);

        if ($realPath === false || !str_starts_with($realPath, realpath($baseDir))) {
            return null;
        }

        return $realPath;
    }

    private function getMimeType(string $filename): string
    {
        $type = 'application/octet-stream';

        if (function_exists('mime_content_type')) {
            $type = mime_content_type($filename);

            if ($type !== false) {
                return $type;
            }
        }

        if (class_exists('\League\MimeTypeDetection\FinfoMimeTypeDetector')) {
            $detector = new FinfoMimeTypeDetector();

            $type = $detector->detectMimeTypeFromPath($filename);

            if ($type !== null) {
                return $type;
            }
        }

        return $type;
    }
}
