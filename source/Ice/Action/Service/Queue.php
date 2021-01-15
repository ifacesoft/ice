<?php

namespace Ice\Action\Service;

use Ice\Core\Action_Service;
use Ice\Helper\Console;
use Ice\Helper\Date;
use Ice\Model\Queue_Task;

class Queue extends Action_Service
{
    /**
     * @param array $input
     * @throws \Ice\Core\Exception
     * @throws \Ice\Exception\Console_Run
     * @throws \Exception
     */
    public function service(array $input)
    {
        $dataProvider = $this->getServiceDataProvider();

        foreach ($this->getConfigData('queue_groups', []) as $queueGroup) {
            foreach ($queueGroup['queues'] as $queueName => $queue) {
                if (!empty($queue['child_classes'])) {
                    $notStartedChildTaskCount = Queue_Task::createQueryBuilder()
                        ->is('/active')
                        ->lt('/scheduled_at', Date::get())
                        ->isNull('/started_at')
                        ->isNull('/finished_at')
                        ->in('/job_class', (array)$queue['child_classes'])
                        ->func(['COUNT' => 'not_started_child_task_count'], '*')
                        ->getSelectQuery(null)
                        ->getValue(null, -1);

                    $notStartedChildTaskMaxCount = isset($queue['not_started_child_task_max_count']) ? $queue['not_started_child_task_max_count'] : 100;

                    if ($notStartedChildTaskMaxCount <= $notStartedChildTaskCount) {
                        continue;
                    }
                }

                $queueKey = 'queue_' . $queueName;

                $pattern = $queueKey . '/';

                $taskCount = isset($queue['task_count']) ? $queue['task_count'] : 1;

                $newTaskCount = $taskCount - count($dataProvider->getKeys($pattern));

                if ($newTaskCount <= 0) {
                    continue 2;
                }

                $newTaskQuery = Queue_Task::createQueryBuilder()
                    ->is('/active')
                    ->lt('/scheduled_at', Date::get())
                    ->isNull('/started_at')
                    ->isNull('/finished_at')
                    ->in('/job_class', (array)$queue['job_classes'])
                    ->desc('/priority')
                    ->asc('left_key')
                    ->limit($newTaskCount)
                    ->getSelectQuery(['/pk', '/job_class']);

                foreach ($newTaskQuery->getRows(-1) as $task) {
                    Console::run('vendor/bin/ice ' . escapeshellarg($task['queue_task_job_class']) . ' taskPk=' . $task['queue_task_pk'] . ' queueKey=' . $queueKey . ' wait=' . $queue['task_wait'], true, true);
                }
            }

            usleep(isset($queueGroup['group_usleep']) ? $queueGroup['group_usleep'] : 0);
        }
    }
}
