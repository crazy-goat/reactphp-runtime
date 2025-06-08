<?php

namespace CrazyGoat\ReactPHPRuntime\Metrics\Formatter;

use CrazyGoat\ReactPHPRuntime\Metrics\MetricsCollection;

class TextMetricsFormatter implements MetricsFormatterInterface
{

    public function format(MetricsCollection $collection): string
    {
        $results = [];

        foreach ($collection->getMetrics() as $prefix => $metrics) {
            foreach ($metrics as $metric => $value) {
                $metric = str_replace('_', ' ', $metric);

                $results[] = ucwords(strtolower("{$prefix} {$metric}")).": ".$value;
            }
        }

        return implode("\n", $results);
    }

    public function contentType(): string
    {
        return 'text/plain';
    }
}