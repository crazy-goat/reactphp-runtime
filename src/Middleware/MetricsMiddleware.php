<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\MetricsFormatterInterface;
use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\TextMetricsFormatter;
use CrazyGoat\ReactPHPRuntime\Metrics\MetricsInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class MetricsMiddleware implements MiddlewareInterface
{
    use ReturnNextResponse;

    private MetricsFormatterInterface $formatter;

    public function __construct(private string $metricsUrl = '', private ?MetricsInterface $metrics = null, ?MetricsFormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new TextMetricsFormatter();
    }

    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface|ResponseInterface
    {
        if (!$this->metrics instanceof MetricsInterface || $this->metricsUrl === '') {
            return $this->returnResponse($next($request));
        }

        if ($request->getUri()->getPath() === $this->metricsUrl) {
            return new Response(
                StatusCodeInterface::STATUS_OK,
                ['Content-Type' => $this->formatter->contentType()],
                $this->formatter->format($this->metrics->getMetrics()),
            );
        }

        $this->metrics->incrementConnections();
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);
        $this->metrics->incrementProcessingTime($endTime - $startTime);

        return $this->returnResponse($response);
    }
}
