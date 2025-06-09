<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Tests;

use CrazyGoat\ReactPHPRuntime\ServerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ServerFactoryTest extends TestCase
{
    private RequestHandlerInterface $handler;
    private LoopInterface $loop;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->loop = $this->createMock(LoopInterface::class);
        $this->loop->expects($this->never())->method('run');
        Loop::set($this->loop);
    }

    public function testCreateServerWithDefaultOptions(): void
    {
        $factory = new ServerFactory([], self::createMock(KernelInterface::class));
        $loop = $factory->createServer($this->handler);

        self::assertInstanceOf(LoopInterface::class, $loop);
        self::assertSame(ServerFactory::getDefaultOptions(), $factory->getOptions());
    }

    public function testCreateServerWithOptions(): void
    {

        $options = [
            ...ServerFactory::getDefaultOptions(),
            'host' => '0.0.0.0',
            'port' => '9999',
        ];
        $factory = new ServerFactory($options, self::createMock(KernelInterface::class));
        $factory->createServer($this->handler);

        self::assertSame($options, $factory->getOptions());
    }
}
