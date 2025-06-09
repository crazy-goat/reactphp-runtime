#!/usr/bin/env sh

REACT_ROOT_DIR="$(pwd)/public"
export REACT_HOST=0.0.0.0
export REACT_PORT=8080
export APP_ENV=prod
export APP_DEBUG=0
export APP_RUNTIME=CrazyGoat\\ReactPHPRuntime\\Standalone\\Runtime
export REACT_ROOT_DIR

echo "Starting http server, visit http://$REACT_HOST:$REACT_PORT"
php ./public/index.php