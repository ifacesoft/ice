<?php

namespace ice\action;

use ice\core\action\Service;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.11.13
 * Time: 14:40
 */
class Service_Status extends Service
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
            case Service::STATUS_ON:
                echo 'Сервис "' . $actionService->getClassName() . '" запускается' . "\n";
                break;
            case Service::STATUS_RUN:
                echo 'Сервис "' . $actionService->getClassName() . '" запущен и работает' . "\n";
                break;
            case Service::STATUS_OFF:
                echo 'Сервис "' . $actionService->getClassName() . '" остановлен' . "\n";
                break;
            default:
                echo 'Статус сервиса "' . $actionService->getClassName() . '" не определен' . "\n";
                break;
        }
    }
}