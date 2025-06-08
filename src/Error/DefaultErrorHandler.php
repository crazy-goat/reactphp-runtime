<?php

namespace CrazyGoat\ReactPHPRuntime\Error;

class DefaultErrorHandler implements ErrorHandlerInterface
{
    public function __invoke(\Throwable $exception): void
    {
        echo $exception->getMessage() . PHP_EOL . $exception->getTraceAsString();
    }
}