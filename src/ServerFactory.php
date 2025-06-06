<?php

namespace CrazyGoat\ReactPHPRuntime;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Filesystem\Factory;
use React\Filesystem\Filesystem;
use React\Http\HttpServer;
use React\Http\Io\MiddlewareRunner;
use React\Socket\SocketServer;

class ServerFactory
{
    private const DEFAULT_OPTIONS = [
        'host' => '127.0.0.1',
        'port' => 8080,
        'document_root_dir' => '',
    ];
    private array $options;

    public static function getDefaultOptions(): array
    {
        return self::DEFAULT_OPTIONS;
    }

    public function __construct(array $options = [])
    {
        $options['host'] = $options['host'] ?? $_SERVER['REACT_HOST'] ?? $_ENV['REACT_HOST'] ?? self::DEFAULT_OPTIONS['host'];
        $options['port'] = $options['port'] ?? $_SERVER['REACT_PORT'] ?? $_ENV['REACT_PORT'] ?? self::DEFAULT_OPTIONS['port'];
        $options['document_root_dir'] = $options['document_root_dir'] ?? $_SERVER['DOCUMENT_ROOT_DIR'] ?? $_ENV['DOCUMENT_ROOT_DIR'] ?? self::DEFAULT_OPTIONS['document_root_dir'];

        $this->options = array_replace_recursive(self::DEFAULT_OPTIONS, $options);
    }

    public function createServer(RequestHandlerInterface $requestHandler): LoopInterface
    {
        $loop = Loop::get();
        $loop->addSignal(SIGTERM, function (int $signal) {
            exit(128 + $signal);
        });

        $server = new HttpServer(
            $loop,
            new StaticFileMiddleware($this->options['document_root_dir']),
            function (ServerRequestInterface $request) use ($requestHandler) {
                return $requestHandler->handle($request);
            }
        );

        $listen = sprintf('%s:%s', $this->options['host'], $this->options['port']);
        echo "Listening on $listen\nYou can check your service on http://{$listen}\n";
        $socket = new SocketServer($listen, [], $loop);
        $server->listen($socket);
        $server->on('error', function (\Throwable $e) {
            echo $e->getMessage().PHP_EOL.$e->getTraceAsString();
        });
        return $loop;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
