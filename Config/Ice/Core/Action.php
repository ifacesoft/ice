<?php
use Ice\Helper\Console;

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
    'Ice\Action\Front_Cli' => [
        'inputDefaults' => [
            'action' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'Ice:Module_Deploy',
                        'title' => 'Action [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => [
                                'params' => '/^[:a-z]+$/i',
                                'message' => 'Action mast conteints only letters and sign ":"'
                            ]
                        ]
                    ]
                );
            }
        ]
    ],
];