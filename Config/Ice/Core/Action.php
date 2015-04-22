<?php

return [
    'Ice\Action\Resources' => [
        'input' => [
            'resources' => [
                'default' => [
                    'modules' => [
                        'Ice' => [
                            'vendor_js' => [
                                'path' => 'js/vendor/',
                                'js' => ['-modernizr-2.8.3.min.js'],
                                'css' => [],
                                'isCopy' => false,
                            ],
                            'vendor_css' => [
                                'path' => 'css/vendor/',
                                'js' => [],
                                'css' => ['empty.css'],
                                'isCopy' => false,
                            ],
                            'vendor' => [
                                'path' => 'vendor/',
                                'js' => [],
                                'css' => [],
                                'isCopy' => false,
                            ],
                            'common' => [
                                'path' => '',
                                'js' => [],
                                'css' => ['css/flags.css', 'css/preloader.css'],
                                'isCopy' => false,
                            ],
                            'module' => [
                                'path' => 'Ice/',
                                'js' => ['Helper/String.js', 'Widget/Form.js', 'Widget/Menu.js', 'Widget/Data.js'],
                                'css' => [],
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
];