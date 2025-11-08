#!/bin/bash

cd "$(dirname "$0")/api" || exit 1

case "${1:-.}" in
    "")
        /home/void/.config/herd-lite/bin/php ./vendor/bin/phpunit --colors
        ;;
    *)
        /home/void/.config/herd-lite/bin/php ./vendor/bin/phpunit "$1" --colors
        ;;
esac
