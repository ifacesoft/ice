<?php

namespace Ice\Core\Action;

use Ice\Core\Action;
use Ice\DataProvider\Cli;
use Ice\Helper\Date;
use Ice\Helper\Logger;

class Job extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => ['task' => ['providers' => Cli::class]],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        // TODO: Implement run() method.
    }

    protected function jobStart($task)
    {
        $task->set(['/started_at' => Date::get()])->save();
    }

    protected function jobSuccess($task)
    {
        $task->set([
            '/finished_at' => Date::get(),
            '/status__fk' => 4
        ])->save();
    }

    protected function jobError($task, \Exception $e)
    {
        $task->set([
            '/finished_at' => Date::get(),
            '/status__fk' => 5,
            'error' => Logger::getMessage($e)
        ])->save();
    }
}