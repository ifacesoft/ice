<?php

namespace Ice\Core;

use Ice\Action\Service\Queue;
use Ice\DataProvider\Cli;
use Ice\DataProvider\Redis;
use Ice\Exception\DataSource_Deadlock;
use Ice\Exception\Error;
use Ice\Helper\Date;
use Ice\Helper\Directory;
use Ice\Helper\Logger as Helper_Logger;
use Ice\Model\Queue_Task;

abstract class Action_Job extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        $config = parent::config();

        $config['input']['taskPk'] = ['providers' => Cli::class];
        $config['input']['queueKey'] = ['providers' => Cli::class];
        $config['input']['force'] = ['providers' => Cli::class, 'default' => 0];
        $config['input']['required'] = ['providers' => Cli::class, 'default' => 0];
        $config['input']['wait'] = ['providers' => Cli::class, 'default' => 1];

        return $config;
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     * @throws Error
     * @throws \Exception
     */
    public function run(array $input)
    {
        $dataProvider = $this->getDProvider(Redis::class, 'default', Queue::class);

        $pattern = $input['queueKey'] . '/';

        $key = $input['taskPk'] ? $pattern . $input['taskPk'] : rtrim($pattern, '/');

        if ($dataProvider->get($key) && !$input['force']) {
            throw new Error(['Task # {$0} runned', $input['taskPk']]);
        }

        $dataProvider->set([$key => $key], 600);

        /** @var Queue_Task $task */
        $task = Queue_Task::createQueryBuilder()
            ->is('/active')
            ->isNull('/started_at', [], QueryBuilder::SQL_LOGICAL_AND, !$input['force'])
            ->pk($input['taskPk'])
            ->eq(['/job_class' => get_class($this)])
            ->getSelectQuery(['/pk', 'params__json', 'user__fk', '/priority'])
            ->getModel();

        if (!$task) {
            throw new Error(['Task # {$0} not found', $input['taskPk']]);
        }

        $task->set([
            '/started_at' => Date::get(),
            'errors' => null
        ])->save();

        $logger = $task->getLogger();

        try {
            ob_start();

            $logger->info('Start job #' . $task->getPkValue(), Logger::INFO);

            sleep($input['wait']);
            $task->set(['result' => $this->job($task)]);

            $logger->info('Finish job #' . $task->getPkValue() . ' successfully', Logger::SUCCESS);

            $task->set([
                '/finished_at' => Date::get(),
                'log' => ob_get_clean()
            ])->save();

            if (Environment::getInstance()->isProduction()) {
                // todo: будем удалять через гарбадж коллектор
                Directory::remove($task->getTempDir());
            }

            $dataProvider->delete($key);

            return [];
        } catch (DataSource_Deadlock $e) {
            if (!$input['required']) {
                ob_clean();

                sleep(rand(30, 60));

                $input['required'] = 1;

                return $this->run($input);
            }
        } catch (\Exception $e) {
            $dataProvider->delete($key);

            $logger->info('Finish job #' . $task->getPkValue() . ' with error', Logger::DANGER);

            $task->set([
                '/finished_at' => Date::get(),
                'log' => ob_get_clean(),
                'errors' => Helper_Logger::getMessage($e)
            ]);

            $this->transacionRestart(
                function () use ($task) {
                    $task->save();
                }
            );

            throw $e;
        }
    }

    /**
     * @param Queue_Task $task
     * @return array
     */
    abstract public function job(Queue_Task $task);
}