<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\DataProvider\Cli;
use Ice\DataProvider\Redis;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;

class Worker_Status extends Action
{
    /**
     * @return array
     */
    protected static function config()
    {
        $config = parent::config();

        $config['input']['worker_key'] = ['providers' => ['default', Router::class, Cli::class]];
        $config['input']['action'] = ['providers' => ['default', Request::class, Cli::class]];

        return $config;
    }

    public function run(array $input)
    {
        $provider = Redis::getInstance('default', $input['action']);

        return $provider->hGet($input['worker_key']);
    }
}
