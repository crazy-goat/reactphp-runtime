<?php

declare(strict_types=1);

namespace CrazyGoat\ReactPHPRuntime\Standalone;

use CrazyGoat\ReactPHPRuntime\Metrics\BasicMetric;
use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\TextMetricsFormatter;
use CrazyGoat\ReactPHPRuntime\Middleware\ErrorMiddleware;
use CrazyGoat\ReactPHPRuntime\Middleware\MetricsMiddleware;
use CrazyGoat\ReactPHPRuntime\Middleware\StaticFileMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Symfony\Component\HttpKernel\KernelInterface;

class ServerFactory
{
    private const DEFAULT_OPTIONS = [
        'host' => '0.0.0.0',
        'port' => 8080,
        'root_dir' => '',
        'metrics_interval' => 5,
        'metrics_path' => '',
        'metrics_formatter' => TextMetricsFormatter::class,
    ];

    /** @var array<string, int|string|float|null> */
    private array $options;

    /**
     * @return array<string, int|string|float|null>
     */
    public static function getDefaultOptions(): array
    {
        return self::DEFAULT_OPTIONS;
    }

    /**
     * @param mixed[] $options
     */
    public function __construct(array $options, private KernelInterface $kernel)
    {
        $this->options['host'] = $this->getOption('host', 'REACT_HOST', $options, '0.0.0.0');
        $this->options['port'] = $this->getOption('port', 'REACT_PORT', $options, 8080);
        $this->options['root_dir'] = $this->getOption('root_dir', 'REACT_ROOT_DIR', $options, '');
        $this->options['metrics_interval'] = $this->getOption('metrics_interval', 'REACT_METRIC_INTERVAL', $options, 5);
        $this->options['metrics_path'] = $this->getOption('metrics_path', 'REACT_METRICS_PATH', $options, '');
        $this->options['metrics_formatter'] = $this->getOption('metrics_formatter', 'REACT_METRICS_FORMATTER', $options, TextMetricsFormatter::class);
    }

    /**
     * @param mixed[] $options
     */
    private function getOption(string $name, string $envName, array $options, null|int|string|float $default = null): null|int|string|float
    {
        $value = $options[$name] ?? $_SERVER[$envName] ?? $_ENV[$envName] ?? self::DEFAULT_OPTIONS[$name] ?? $default;

        if (is_float($value) || is_int($value) || is_string($value) || is_null($value)) {
            return $value;
        }
        throw new \InvalidArgumentException("Invalid option $name");
    }

    public function createServer(RequestHandlerInterface $requestHandler): LoopInterface
    {
        $loop = Loop::get();
        $loop->addSignal(SIGTERM, function (int $signal): void {
            exit(128 + $signal);
        });

        $server = new HttpServer(
            $loop,
            new ErrorMiddleware($this->kernel->isDebug()),
            new MetricsMiddleware('/metrics', new BasicMetric()),
            new StaticFileMiddleware(strval($this->options['root_dir'])),
            fn(ServerRequestInterface $request): ResponseInterface => $requestHandler->handle($request),
        );

        $listen = sprintf('%s:%s', $this->options['host'], $this->options['port']);
        $socket = new SocketServer($listen, [], $loop);
        $server->listen($socket);
        return $loop;
    }

    /**
     * @return array|float[]|int[]|string[]|null[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
