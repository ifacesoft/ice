<?php

namespace ice\action;

use ice\core\action\Service;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.11.13
 * Time: 14:39
 */
class Service_Stop extends Service
{

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        $actionService = $this->getActionService($input);

        switch ($actionService->getStatus()) {
            case Service::STATUS_OFF:
                echo 'Сервис "' . $actionService->getClassName() . '" уже остановлен' . "\n";
                break;
            default:
                $actionService->setStatus(Service::STATUS_OFF);
                echo 'Сервису "' . $actionService->getClassName() . '" послан сигнал для останова' . "\n";
        }
    }
}