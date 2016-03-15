<?php

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Widget_Security;
use Ice\Helper\Date;
use Ice\Model\Token;

class Security_LoginPassword_RegisterConfirm_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $logger = $securityForm->getLogger();

        try {
            /** @var Token $token */
            $token = Token::createQueryBuilder()
                ->eq(['/' => $securityForm->getValue('token')])
                ->gt('/expired', Date::get())
                ->getSelectQuery('*')
                ->getModel();

            if (!$token) {
                return [
                    'error' => $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true)
                ];
            }

            $this->registerConfirm($token, $input);

            return array_merge(
                ['success' => $logger->info('Регистрация успешно подтверждена', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (\Exception $e) {
            $logger->error('Подтверждение не удалось', __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Подтверждение не удалось', Logger::DANGER, true)
            ];
        }
    }
}