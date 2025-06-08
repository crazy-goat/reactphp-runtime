<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Metrics;

class MetricsCollection
{
    private array $metrics = [];

    public function addGauge(string $prefix, string $label, float|int $value): void
    {
        $this->metrics[$prefix][$label] = $value;
    }

    public function getMetrics(): array
    {
        return $this->metrics;
    }

}
