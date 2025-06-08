<?php

namespace CrazyGoat\ReactPHPRuntime\Metrics;

use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\MetricsFormatterInterface;

interface MetricsInterface
{
    public function setInterval(int $interval): void;
    public function incrementConnections(): void;
    public function incrementProcessingTime(float $time): void;
    public function setMemoryUsage(int $memoryUsage): void;

    public function getMetrics(): MetricsCollection;
}