#!/bin/sh -e
echo "no" | pecl install channel://pecl.php.net/redis-2.2.5
echo "no" | pecl install channel://pecl.php.net/apcu-4.0.7
echo "extension = redis.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "extension = apc.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "apc.enable_cli = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
fi