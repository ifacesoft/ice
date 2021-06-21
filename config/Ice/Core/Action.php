<?php

return [
    'Ice\Action\Resource_Css' => [
        'input' => [
            'resources' => [
                'default' => [
                    'modules' => [
                        'Ice' => [
                            'vendor_css' => [
                                'path' => 'css/vendor/',
                                'css' => ['alxlit/bootstrap-chosen/bootstrap-chosen.css'],
                                'isCopy' => false,
                                'css_replace' => ['url("', 'url("/resource/node_modules/drmonty-chosen/'],
                            ],
                            'vendor' => [
                                'path' => 'vendor/',
                                'css' => [],
                                'isCopy' => false,
                            ],
                            'common' => [
                                'path' => '',
                                'css' => ['css/flags.css', 'css/preloader.css'],
                                'isCopy' => false,
                            ],
                            'module' => [
                                'path' => 'Ice/',
                                'css' => ['Widget/Form/File.css'],
                                'isCopy' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'Ice\Action\Resource_Js' => [
        'input' => [
            'resources' => [
                'default' => [
                    'modules' => [
                        'Ice' => [
                            'vendor_js' => [
                                'path' => 'js/vendor/',
                                'js' => ['-modernizr-2.8.3.min.js'],
                                'isCopy' => false,
                            ],
                            'vendor' => [
                                'path' => 'vendor/',
                                'js' => [],
                                'isCopy' => false,
                            ],
                            'common' => [
                                'path' => '',
                                'js' => [],
                                'isCopy' => false,
                            ],
                            'module' => [
                                'path' => 'Ice/',
                                'js' => ['Helper/String.js', 'Core/Base64.js', 'Core/Ice.js', 'Core/Widget.js'],
                                'isCopy' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'Ice\Action\Cache_Hit' => [
        'input' => [
            'routeNames' => [
                'default' => ['ice_test']
            ]
        ]
    ],
];