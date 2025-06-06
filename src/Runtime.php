<?php

namespace CrazyGoat\ReactPHPRuntime;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\GenericRuntime;
use Symfony\Component\Runtime\RunnerInterface;

/**
 * A runtime for ReactPHP.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Runtime extends GenericRuntime
{
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof HttpKernelInterface) {
            return new Runner(new ServerFactory($this->options), $application);
        }

        return parent::getRunner($application);
    }
}
