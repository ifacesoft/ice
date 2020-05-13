<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Helper\Console;

class Sami extends Action
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
                'vendor' => ['default' => 'sami/sami'],
                'command' => ['default' => '/sami.php'],
                'config' => ['default' => 'vendor/sami.php']
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
        $command = VENDOR_DIR . $input['vendor'] . $input['command'];
        $config = \getConfigDir() . $input['config'];

        Console::run(
            [
                'php ' . $command . ' update ' . $config
            ]
        );
    }
}
