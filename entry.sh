#!/bin/sh
set -e

echo "ТЕСТОВЫЙ СЦЕНАРИЙ"
php test.php

echo "ТЕСТЫ"
./vendor/bin/phpunit
