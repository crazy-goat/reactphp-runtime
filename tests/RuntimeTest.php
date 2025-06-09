<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Tests;

use CrazyGoat\ReactPHPRuntime\Standalone\Runner;
use CrazyGoat\ReactPHPRuntime\Standalone\Runtime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class RuntimeTest extends TestCase
{
    public function testGetRunnerCreatesARunnerForRequestHandlers(): void
    {
        $options = [];
        $runtime = new Runtime($options);

        $application = $this->createMock(KernelInterface::class);
        $runner = $runtime->getRunner($application);

        self::assertInstanceOf(Runner::class, $runner);
    }
}
