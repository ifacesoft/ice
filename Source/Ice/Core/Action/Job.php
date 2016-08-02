<?php

namespace Ice\Core\Action;

use Ebs\Model\Worker_Task;
use Ice\Core\Action;
use Ice\Core\DataSource;
use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\DataProvider\Cli;
use Ice\Exception\Error;
use Ice\Helper\Date;
use Ice\Helper\Logger as Helper_Logger;

abstract class Job extends Action
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
     * @throws Error
     */
    public function run(array $input)
    {
        $logger = $this->getLogger();

        ob_start();

        $logger->info('Start job #' . $input['task'], Logger::INFO);

        /** @var Worker_Task $task */
        $task = Worker_Task::getModel($input['task'], ['/pk', 'params__json', 'worker_queue__fk']);

        if (!$task) {
            throw new Error(['Task # {$0} not found', $input['task']]);
        }

        $this->start($task);

        $dataSource = DataSource::getInstance();

        try {
            $dataSource->beginTransaction();

            $this->job($task, $logger);

            $dataSource->commitTransaction();

            $this->success($task);
            $logger->info('Finish job #' . $input['task'] . ' successfully', Logger::SUCCESS);
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction();
            $this->error($task, $e);
            $logger->info('Finish job #' . $input['task'] . ' with error', Logger::DANGER);
        }

        $this->log($task, ob_get_clean());
    }

    protected function start(Worker_Task $task)
    {
        $task->set(['/started_at' => Date::get()])->save();
    }

    abstract protected function job(Worker_Task $task, Logger $logger);

    protected function success(Worker_Task $task)
    {
        $task->set([
            '/finished_at' => Date::get(),
            '/status__fk' => 4
        ])->save();
    }

    protected function error(Worker_Task $task, \Exception $e)
    {
        $task->set([
            '/finished_at' => Date::get(),
            '/status__fk' => 5,
            'error' => Helper_Logger::getMessage($e)
        ])->save();
    }

    protected function log(Worker_Task $task, $log)
    {
        $task->set(['log' => $log])->save();
    }
}