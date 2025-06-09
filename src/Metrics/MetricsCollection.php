<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Metrics;

class MetricsCollection
{
    /** @var array<string, array<string| float|int>> */
    private array $metrics = [];

    public function addGauge(string $prefix, string $label, float|int $value): void
    {
        $this->metrics[$prefix][$label] = $value;
    }

    /** @return array<string, array<string| float|int>>   */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

}
