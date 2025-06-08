<?php

namespace CrazyGoat\ReactPHPRuntime\Error;

interface ErrorHandlerInterface
{
    public function __invoke(\Throwable $exception): void;
}