<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Tests;

use CrazyGoat\ReactPHPRuntime\Runner;
use CrazyGoat\ReactPHPRuntime\Runtime;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

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
