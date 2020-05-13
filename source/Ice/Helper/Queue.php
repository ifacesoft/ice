<?php

namespace Ice\Helper;

use Ice\Core\Model as Core_Model;
use Ice\Model\Queue_Task;

class Queue
{
    /**
     * @param Core_Model|string $jobClass
     * @param array $params
     * @param Queue_Task|null $parentTask
     * @param int $userKey
     * @param null $priority
     * @return Core_Model|Queue_Task
     * @throws \Exception
     * @throws \Ice\Core\Exception
     */
    public static function addTask($jobClass, $params = [], Queue_Task $parentTask = null, $userKey = 0, $priority = null)
    {
        $taskData = [
            '/job_class' => $jobClass,
            'params' => $params,
            'user__fk' => $parentTask
                ? $parentTask->get('user__fk')
                : $userKey,
            '/scheduled_at' => isset($params['queue_task_scheduled_at'])
                ? Date::get($params['queue_task_scheduled_at'])
                : Date::get()
        ];

        if ($priority === null) {
            if ($parentTask) {
                $taskData['/priority'] = $parentTask->get('/priority');
            }
        } else {
            $taskData['/priority'] = (int)$priority;
        }

        return Model_Tree_NestedSets::insert(
            Queue_Task::class,
            $taskData,
            $parentTask ? $parentTask->getPkValue() : null
        );
    }
}