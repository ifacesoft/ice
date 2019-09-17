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
                                'css' => ['empty.css'],
                                'isCopy' => false,
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
                                'css' => ['Widget/Menu.css', 'Core/Widget/Form/File.css'],
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
                                'js' => ['Helper/String.js', 'Widget/Form.js', 'Widget/Menu.js', 'Widget/Data.js'],
                                'isCopy' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'Ice\Action\Admin_Navigation' => [
        'input' => [
            'items' => [
                'default' => [
                    ['routeName' => 'ice_admin_database']
                ]
            ]
        ]
    ],
    'Ice\Action\Cache_Hit' => [
        'input' => [
            'routeNames' => [
                'default' =>['ice_test']
            ]
        ]
    ],
];