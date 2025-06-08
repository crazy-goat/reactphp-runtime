<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Metrics\Formatter;

use CrazyGoat\ReactPHPRuntime\Metrics\MetricsCollection;

class JsonMetricsFormatter implements MetricsFormatterInterface
{
    public function format(MetricsCollection $collection): string
    {
        return json_encode($collection->getMetrics(), JSON_THROW_ON_ERROR);
    }

    public function contentType(): string
    {
        return 'application/json';
    }
}
