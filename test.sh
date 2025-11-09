#!/bin/bash

cd "$(dirname "$0")" || exit 1

if command -v docker &> /dev/null && docker compose ps api &> /dev/null 2>&1; then
    sudo docker compose exec -T api mkdir -p /var/www/html/storage/framework/testing 2>/dev/null
    sudo docker compose exec -T api chmod 777 /var/www/html/storage/framework/testing 2>/dev/null

    sudo docker compose exec -T api touch /var/www/html/storage/framework/testing/testing.sqlite 2>/dev/null
    sudo docker compose exec -T api chmod 666 /var/www/html/storage/framework/testing/testing.sqlite 2>/dev/null

    sed -i 's|DB_DATABASE" value=".*"|DB_DATABASE" value="/var/www/html/storage/framework/testing/testing.sqlite"|' api/phpunit.xml

    case "${1:-.}" in
        "")
            sudo docker compose exec -T api php artisan test --colors
            ;;
        *)
            sudo docker compose exec -T api php artisan test "$1" --colors
            ;;
    esac

    sed -i 's|DB_DATABASE" value=".*"|DB_DATABASE" value=":memory:"|' api/phpunit.xml
else
    case "${1:-.}" in
        "")
            cd api && php artisan test --colors
            ;;
        *)
            cd api && php artisan test "$1" --colors
            ;;
    esac
fi

