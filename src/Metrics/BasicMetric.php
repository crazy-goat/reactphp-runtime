<?php

namespace CrazyGoat\ReactPHPRuntime\Metrics;

use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\JsonMetricsFormatter;
use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\MetricsFormatterInterface;

class BasicMetric implements MetricsInterface
{
    private int $totalConnectionCount = 0;
    private ?float $maxProcessTime = null;
    private ?float $minProcessTime = null;
    private float $maxMemoryUsage = 0.0;
    private int $period = 0;

    private int $latestConnectionCount = 0;
    private float $latestProcessingTime = 0.0;
    private ?float $latestMaxProcessTime = null;
    private ?float $latestMinProcessTime = null;
    private int $latestMaxMemoryUsage = 0;
    private int $latestMemoryUsage = 0;
    private MetricsFormatterInterface $metricFormatter;

    public function __construct(private int $interval = 5, ?MetricsFormatterInterface $metricFormatter = null)
    {
        $this->refreshPeriod();
        $this->metricFormatter = $metricFormatter ?? new JsonMetricsFormatter();
    }

    public function setInterval(int $interval): void
    {
        $this->interval = $interval;
    }

    public function incrementConnections(): void
    {
        $this->latestConnectionCount++;
        $this->totalConnectionCount++;
    }

    public function incrementProcessingTime(float $time): void
    {
        $this->refreshPeriod();
        $this->latestProcessingTime += $time;
        $this->maxProcessTime = max($this->maxProcessTime ?? 0.0, $time);
        $this->latestMaxProcessTime = max($this->latestMaxProcessTime ?? 0.0, $time);
        $this->minProcessTime = min($this->minProcessTime ?? PHP_FLOAT_MAX, $time);
        $this->latestMinProcessTime = min($this->latestMinProcessTime ?? PHP_FLOAT_MAX, $time);
    }

    public function getMetrics(): MetricsCollection
    {
        $this->refreshPeriod();

        $metricsCollection = new  MetricsCollection($this->metricFormatter);
        $metricsCollection->addGauge('total', 'connections', $this->totalConnectionCount);
        $metricsCollection->addGauge('total', 'max_process_time',  $this->maxProcessTime ?? 0.0);
        $metricsCollection->addGauge('total', 'min_process_time', $this->minProcessTime ?? 0.0);
        $metricsCollection->addGauge('total', 'max_memory_usage', $this->maxMemoryUsage);

        $metricsCollection->addGauge('current', 'interval',$this->interval);
        $metricsCollection->addGauge('current', 'connections', $this->latestConnectionCount);
        $metricsCollection->addGauge('current', 'duty_cycle', $this->dutyCycle());
        $metricsCollection->addGauge('current', 'request_rate', $this->rate());
        $metricsCollection->addGauge('current', 'max_process_time', $this->latestMaxProcessTime ?? 0.0);
        $metricsCollection->addGauge('current', 'min_process_time', $this->latestMinProcessTime ?? 0.0);
        $metricsCollection->addGauge('current', 'avg_process_time', $this->latestConnectionCount === 0 ? 0 : $this->latestProcessingTime/$this->latestConnectionCount,);
        $metricsCollection->addGauge('current', 'max_memory_usage', $this->latestMaxMemoryUsage);
        $metricsCollection->addGauge('current', 'memory_usage', $this->latestMemoryUsage);

        return $metricsCollection;
    }

    private function resetMetrics(): void
    {
        $this->latestConnectionCount = 0;
        $this->latestProcessingTime = 0;
        $this->latestMaxProcessTime = null;
        $this->latestMinProcessTime = null;
        $this->setMemoryUsage(memory_get_usage(true));
    }

    private function dutyCycle(): int
    {
        $duration = microtime(true) - floatval($this->period * $this->interval);
        return ceil(100.0 * $this->latestProcessingTime / $duration);
    }

    private function rate(): int
    {
        $duration = microtime(true) - floatval($this->period * $this->interval);
        return intval(floor($this->latestConnectionCount / $duration));
    }

    /**
     * @return void
     */
    public function refreshPeriod(): void
    {
        $currentPeriod = intval(floor(time() / $this->interval));
        if ($this->period !== $currentPeriod) {
            $this->period = $currentPeriod;
            $this->resetMetrics();
        }
    }

    public function setMemoryUsage(int $memoryUsage): void
    {
        $this->maxMemoryUsage = max($this->maxMemoryUsage, $memoryUsage);
        $this->latestMaxMemoryUsage = max($this->latestMaxMemoryUsage, $memoryUsage);
        $this->latestMemoryUsage = $memoryUsage;
    }
}