<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Metrics\Formatter;

use CrazyGoat\ReactPHPRuntime\Metrics\MetricsCollection;

interface MetricsFormatterInterface
{
    public function format(MetricsCollection $collection): string;
    public function contentType(): string;
}
