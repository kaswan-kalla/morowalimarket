#!/bin/bash
echo "Clearing CodeIgniter cache..."

# Clear writable/cache
rm -rf writable/cache/*
rm -rf writable/debugbar/*
rm -rf writable/logs/*
rm -rf writable/session/*

# Clear OPcache if enabled
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared\n'; } else { echo 'OPcache not enabled\n'; }"

echo "Cache cleared successfully!"
