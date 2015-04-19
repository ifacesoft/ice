<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/../../Source');

return new Sami($iterator, array(
    'title' => 'Ice API',
    'theme' => 'default',
    'build_dir' => __DIR__ . '/../../../_resource/api',
    'cache_dir' => __DIR__ . '/../../../_cache/sami',
    'include_parent_data' => false,
));