<?php

namespace CrazyGoat\ReactPHPRuntime\Metrics\Formatter;

use CrazyGoat\ReactPHPRuntime\Metrics\MetricsCollection;

interface MetricsFormatterInterface
{
    public function format(MetricsCollection $collection): string;
    public function contentType(): string;
}