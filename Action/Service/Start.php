<?php

namespace ice\action;

use ice\core\action\Service;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.11.13
 * Time: 14:35
 */
class Service_Start extends Service
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
                echo 'Сервис "' . $actionService->getClassName() . '" уже запускается' . "\n";
                break;
            case Service::STATUS_RUN:
                echo 'Сервис "' . $actionService->getClassName() . '" уже запущен' . "\n";
                break;
            default:
                echo 'Запускается экшин-сервис "' . $actionService->getClassName() . '" ... ' . "\n";

                $actionService->setStatus(Service::STATUS_ON);

                try {
                    $actionService::call($input);

                    if ($actionService->getStatus() == Service::STATUS_RUN) {
                        echo 'успешно запущен :)' . "\n";
                    } else {
                        echo 'не получен код ответа сервиса.. возможно он запущен :|' . "\n";
                        $actionService->setStatus(Service::STATUS_OFF);
                    }
                } catch (\Exception $e) {
                    echo 'ошибка при запуске :(' . "\n";
                    Debug::handleException($e);
                }
        }
    }
}