#!/bin/sh -e

echo "no" | pecl install channel://pecl.php.net/apcu-4.0.7
echo "extension = apc.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "apc.enable_cli = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
pyrus install pecl/redis && pyrus build pecl/redis
echo "extension = redis.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
