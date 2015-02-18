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