<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Helper\Console;

class CodeSniffer extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'input' => [
                'vendor' => ['default' => 'squizlabs/php_codesniffer'],
                'command' => [
                    'default' => [
                        'phpcbf' => '/scripts/phpcbf',
                        'phpcs' => '/scripts/phpcs'
                    ]
                ]
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        $phpcbf = VENDOR_DIR . $input['vendor'] . $input['command']['phpcbf'];
        $phpcs = VENDOR_DIR . $input['vendor'] . $input['command']['phpcs'];

        Console::run(
            'php ' . $phpcbf . ' ' . Module::getInstance()->get(Module::SOURCE_DIR) . ' --standard=PSR2'
        );

        Console::run(
            'php ' . $phpcs . ' ' . Module::getInstance()->get(Module::SOURCE_DIR) . ' --report-full --standard=PSR2'
        );
    }
}
