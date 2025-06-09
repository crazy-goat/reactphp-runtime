<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Tests;

use CrazyGoat\ReactPHPRuntime\Standalone\Runner;
use CrazyGoat\ReactPHPRuntime\Standalone\ServerFactory;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use Symfony\Component\HttpKernel\KernelInterface;

class RunnerTest extends TestCase
{
    public function testRun(): void
    {
        $handler = function () {};
        $loop = $this->createMock(LoopInterface::class);
        Loop::set($loop);
        $server = new HttpServer($handler); // final, cannot be mocked
        $factory = $this->createMock(ServerFactory::class);
        $application = $this->createMock(KernelInterface::class);

        $factory->expects(self::once())->method('createServer')->willReturn($loop);

        $runner = new Runner($factory, $application);

        self::assertSame(0, $runner->run());
    }
}
