<?php

namespace CrazyGoat\ReactPHPRuntime;

use CrazyGoat\ReactPHPRuntime\Metrics\BasicMetric;
use CrazyGoat\ReactPHPRuntime\Metrics\Formatter\TextMetricsFormatter;
use CrazyGoat\ReactPHPRuntime\Middleware\ErrorMiddleware;
use CrazyGoat\ReactPHPRuntime\Middleware\MetricsMiddleware;
use CrazyGoat\ReactPHPRuntime\Middleware\StaticFileMiddleware;
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
    private array $options;

    public static function getDefaultOptions(): array
    {
        return self::DEFAULT_OPTIONS;
    }

    public function __construct(array $options = [], private KernelInterface $kernel)
    {
        $options['host'] = $this->getOption('host', 'REACT_HOST', $options, '0.0.0.0');
        $options['port'] = $this->getOption('port', 'REACT_PORT', $options, 8080);
        $options['root_dir'] = $this->getOption('root_dir', 'REACT_ROOT_DIR', $options, '');
        $options['metrics_interval'] = $this->getOption('metrics_interval', 'REACT_METRIC_INTERVAL', $options, 5);
        $options['metrics_path'] = $this->getOption('metrics_path', 'REACT_METRICS_PATH', $options, '');

        $this->options = array_replace_recursive(self::DEFAULT_OPTIONS, $options);
    }

    private function getOption(string $name, string $envName, array $options, mixed $default = null): mixed
    {
        return $options[$name] ?? $_SERVER[$envName] ?? $_ENV[$envName] ?? self::DEFAULT_OPTIONS[$name] ?? $default;
    }

    public function createServer(RequestHandlerInterface $requestHandler): LoopInterface
    {
        $loop = Loop::get();
        $loop->addSignal(SIGTERM, function (int $signal) {
            exit(128 + $signal);
        });

        $server = new HttpServer(
            $loop,
            new ErrorMiddleware($this->kernel->isDebug()),
            new MetricsMiddleware('/metrics', new BasicMetric()),
            new StaticFileMiddleware($this->options['root_dir']),
            function (ServerRequestInterface $request) use ($requestHandler) {
                return $requestHandler->handle($request);
            }
        );

        $listen = sprintf('%s:%s', $this->options['host'], $this->options['port']);
        echo "Listening on $listen\nYou can check your service on http://{$listen}\n";
        $socket = new SocketServer($listen, [], $loop);
        $server->listen($socket);
        return $loop;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
