<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Standalone;

use Symfony\Component\HttpKernel\KernelInterface;
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
        if ($application instanceof KernelInterface) {
            return new Runner(new ServerFactory($this->options, $application), $application);
        }

        return parent::getRunner($application);
    }
}
