<?php

namespace CrazyGoat\ReactPHPRuntime\Middleware;

use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\JsonMetricsFormatter;
use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\MetricsFormatterInterface;
use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\TextMetricsFormatter;
use CrazyGoat\ReactPHPRuntime\Metrics\MetricsInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class MetricsMiddleware
{
    private ?MetricsFormatterInterface $formatter;

    public function __construct(private string $metricsUrl = '', private ?MetricsInterface $metrics = null, ?MetricsFormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new TextMetricsFormatter();
    }

    public function __invoke(ServerRequestInterface $request, $next)
    {
        if ($this->metrics === null || $this->metricsUrl === '') {
            return $next($request);
        }

        if ($request->getUri()->getPath() === $this->metricsUrl) {
            return new Response(
                StatusCodeInterface::STATUS_OK,
                ['Content-Type' => $this->formatter->contentType()],
                $this->formatter->format($this->metrics->getMetrics())
            );
        }

        $this->metrics->incrementConnections();;
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);
        $this->metrics->incrementProcessingTime($endTime - $startTime);

        return $response;
    }
}