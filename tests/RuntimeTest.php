<?php

namespace CrazyGoat\ReactPHPRuntime\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use CrazyGoat\ReactPHPRuntime\Runner;
use CrazyGoat\ReactPHPRuntime\Runtime;

class RuntimeTest extends TestCase
{
    public function testGetRunnerCreatesARunnerForRequestHandlers(): void
    {
        $options = [];
        $runtime = new Runtime($options);

        $application = $this->createMock(RequestHandlerInterface::class);
        $runner = $runtime->getRunner($application);

        self::assertInstanceOf(Runner::class, $runner);
    }
}
