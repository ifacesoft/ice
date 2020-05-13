<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Helper\Console;

class Sphinx extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => null, 'layout' => null],
            'actions' => [],
            'input' => [
                'searchd' => ['default' => 'searchd'],
                'indexer' => ['default' => 'indexer'],
                'config' => ['default' => 'vendor/sphinx.conf']
            ],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $config = \getConfigDir() . $input['config'];

        $commands = [
//            $input['searchd'] . ' --stop',
            $input['searchd'] . ' --config ' . $config,
            $input['indexer'] . ' --verbose --rotate --sighup-each --config ' . $config . ' --all',
            $input['searchd'] . ' --status --config ' . $config,
        ];

        Console::run($commands);

        Console::getText('Complete!', Console::C_GREEN);
    }
}