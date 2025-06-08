<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Error;

interface ErrorHandlerInterface
{
    public function __invoke(\Throwable $exception): void;
}
