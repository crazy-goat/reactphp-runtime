# ReactPHP Runtime

A runtime for [ReactPHP](https://reactphp.org/).

If you are new to the Symfony Runtime component, read more in the [main readme](https://github.com/php-runtime/runtime).

## Installation

```
composer require crazy-goat/reactphp-runtime
```

## Usage

Define the environment variable `APP_RUNTIME` for your application. You can also provide
`APP_ENV` and `APP_DEBGU` variables to define runtime environment.

```
 APP_ENV=prod APP_DEBUG=1 APP_RUNTIME=CrazyGoat\\ReactPHPRuntime\\Standalone\\Runtime php ./public/index.ph
```

## Server options

| Option              | Description                                   |                           | Default                                                             |
|---------------------|-----------------------------------------------|---------------------------|---------------------------------------------------------------------|
| `host`              | The host where the server should bind to      | `REACT_HOST`              | `0.0.0.0`                                                           |
| `port`              | The port where the server should be listening | `REACT_PORT`              | `8080`                                                              |
| `root_dir`          | Set the root dir for serving files            | `REACT_ROOT_DIR`          | `""`                                                                |
| `metrics_interval`  | Set metrics refresh interval                  | `REACT_METRIC_INTERVAL`   | `5`                                                                 |
| `metrics_path`      | Set metrics url, empty string disable metrics | `REACT_METRICS_PATH`      | `""`                                                                |        
| `metrics_formatter` | Set metrics format class                      | `REACT_METRICS_FORMATTER` | `\CrazyGoat\ReactPHPRuntime\Metrics\Formatter\TextMetricsFormatter` |
