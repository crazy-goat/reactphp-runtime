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
 APP_ENV=prod APP_DEBUG=1 APP_RUNTIME=CrazyGoat\\ReactPHPRuntime\\Runtime php ./public/index.ph
```

## Server options

| Option    | Description                                                                                | Default |
|-----------|--------------------------------------------------------------------------------------------|---------|
| `host`    | The host where the server should bind to (precedes `REACT_HOST` environment variable)      | `127.0.0.1` |
| `port`    | The port where the server should be listening (precedes `REACT_PORT` environment variable) | `8080`  |
| `document_root_dir` | Set the root dir for serving files (precedes `DOCUMENT_ROOT_DIR` environment variable)            | `""`      |

