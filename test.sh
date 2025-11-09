#!/bin/bash

# Run tests inside the Docker container
cd "$(dirname "$0")" || exit 1

case "${1:-.}" in
    "")
        sudo docker compose exec -T api php ./vendor/bin/phpunit --colors
        ;;
    *)
        sudo docker compose exec -T api php ./vendor/bin/phpunit "$1" --colors
        ;;
esac
