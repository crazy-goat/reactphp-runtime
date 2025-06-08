<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Metrics;

interface MetricsInterface
{
    public function setInterval(int $interval): void;
    public function incrementConnections(): void;
    public function incrementProcessingTime(float $time): void;
    public function setMemoryUsage(int $memoryUsage): void;

    public function getMetrics(): MetricsCollection;
}
