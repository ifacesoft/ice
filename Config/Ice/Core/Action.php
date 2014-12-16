<?php
use Ice\Helper\Console;

return [
    'Ice\Action\Phpdoc_Generate' => [
        'inputDefaults' => [
            'vendor' => 'phpdocumentor/phpdocumentor',
            'script' => 'bin/phpdoc',
            'sourceDir' => MODULE_DIR . 'Source',
            'apiDir' => MODULE_DIR . 'Resource/api/' . basename(MODULE_DIR),
            'tpl' => 'responsive-twig'
        ]
    ],
    'Ice\Action\Phpuml_Generate' => [
        'inputDefaults' => [
            'vendor' => 'zerkalica/phpuml',
            'script' => 'bin/phpuml',
            'sourceDir' => MODULE_DIR . 'Source',
            'umlDir' => MODULE_DIR . 'Resource/uml/' . basename(MODULE_DIR),
        ]
    ],
    'Ice\Action\Phpunit_Run' => [
        'inputDefaults' => [
            'vendor' => 'phpunit/phpunit',
            'script' => 'phpunit',
            'testClasses' => [
                'IceTest'
            ]
        ]
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
    'Ice\Action\Module_Create' => [
        'inputDefaults' => [
            'name' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'MyProject',
                        'title' => 'Module name [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^[a-z]+$/i'
                        ]
                    ]
                );
            },
            'alias' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'Mp',
                        'title' => 'Module alias (short module name, 2-5 letters) [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^[a-z]+$/i'
                        ]
                    ]
                );
            },
            'scheme' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'test',
                        'title' => 'Scheme - database name(not empty and must be exists) [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^[a-z]+$/i'
                        ]
                    ]
                );
            },
            'username' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'root',
                        'title' => 'Database username [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^[a-z]+$/i'
                        ]
                    ]
                );
            },
            'password' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => '',
                        'title' => 'Database username password [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^[a-z]+$/i'
                        ]
                    ]
                );
            },
            'viewRender' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'Smarty',
                        'title' => 'Default view render (Php|Smarty|Twig)  [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^(Php|Smarty|Twig)$/i'
                        ]
                    ]
                );
            },
            'vcs' => function ($param) {
                return Console::getInteractive('Ice\Action\Module_Create', $param,
                    [
                        'default' => 'mercurial',
                        'title' => 'Default version control system (mercurial|git|subversion)  [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^(mercurial|git|subversion)$/i'
                        ]
                    ]
                );
            }
        ]
    ]
];