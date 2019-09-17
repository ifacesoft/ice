<?php

return [
    'Ice\View\Render\Cli' => [],
    'Ice\View\Render\Json' => [],
    'Ice\View\Render\Php' => [],
    'Ice\View\Render\Replace' => [],
    'Ice\View\Render\Smarty' => [
        'dataProviderKey' => 'Ice:Registry/view_render',
        'vendor' => 'smarty/smarty',
        'templates_c' => 'smarty/templates_c/',
        'plugins' => [MODULE_DIR . 'vendor/Smarty/plugins']
    ],
    'Ice\View\Render\Twig' => [
        'vendor' => 'twig/twig',
        'cache' => 'twig/cache/',
    ],
];