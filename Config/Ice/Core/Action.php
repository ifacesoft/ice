<?php
return [
    'Ice\Action\Phpdoc_Generate' => [
        'inputDefaults' => [
            'vendor' => 'phpdocumentor/phpdocumentor',
            'script' => 'bin/phpdoc',
            'sourceDir' => MODULE_DIR . 'Source',
            'apiDir' => MODULE_DIR . 'Resource/api/' . MODULE_BRANCH . '/' . basename(MODULE_DIR)
        ]
    ],
    'Ice\Action\Phpuml_Generate' => [
        'inputDefaults' => [
            'vendor' => 'zerkalica/phpuml',
            'script' => 'bin/phpuml',
            'sourceDir' => MODULE_DIR . 'Source',
            'umlDir' => MODULE_DIR . 'Resource/uml/' . basename(MODULE_DIR)
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
    ]
];