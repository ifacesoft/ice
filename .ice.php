<?php

return [
    'vendor' => 'ifacesoft',
    'name' => 'ice',
    'namespace' => 'Ifacesoft\Ice\Framework\\',
    'alias' => 'Ice',
    'description' => 'Ice Framework',
    'url' => 'http://ice.ifacesoft.iceframework.net',
    'type' => 'module',
    'context' => '',
//    'source' => [
//        'vcs' => 'mercurial',
//        'repo' => 'https://bitbucket.org/ifacesoft/ice'
//    ],
//    'authors' => [
//        [
//            'name' => 'dp',
//            'email' => 'denis.a.shestakov@gmail.com'
//        ]
//    ],
    'pathes' => [
        'config' => 'config/',
        'source' => 'source/',
        'resource' => 'resource/',
    ],
    'environments' => [
        'prod' => [
            'pattern' => '/^ice\.prod\.local$/',
        ],
        'test' => [
            'pattern' => '/^ice\.test\.local$/',
        ],
        'dev' => [
            'pattern' => '/^ice\.dev\.local$/'
        ],
    ],
    'modules' => [
        'ifacesoft/ice-cli' => [],
        'ifacesoft/ice-http' => [],
    ],
];