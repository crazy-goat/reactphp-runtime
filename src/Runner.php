<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

class Runner implements RunnerInterface
{
    public function __construct(private ServerFactory $serverFactory, private KernelInterface $kernel)
    {
    }

    public function run(): int
    {
        $this->kernel->boot();

        $loop = $this->serverFactory->createServer(new RequestHandler($this->kernel));
        $loop->run();

        return 0;
    }
}
