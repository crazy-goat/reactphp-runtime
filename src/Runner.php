<?php

namespace CrazyGoat\ReactPHPRuntime;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

class Runner implements RunnerInterface
{
    private KernelInterface $kernel;
    private ServerFactory $serverFactory;

    public function __construct(ServerFactory $serverFactory, KernelInterface $kernel)
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
