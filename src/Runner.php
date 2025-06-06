<?php

namespace CrazyGoat\ReactPHPRuntime;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

class Runner implements RunnerInterface
{
    private HttpKernelInterface $kernel;
    private ServerFactory $serverFactory;

    public function __construct(ServerFactory $serverFactory, HttpKernelInterface $kernel)
    {
        $this->serverFactory = $serverFactory;
        $this->kernel = $kernel;
    }

    public function run(): int
    {
        $this->kernel->boot();

        $loop = $this->serverFactory->createServer(new RequestHandler($this->kernel));
        $loop->run();

        return 0;
    }
}
