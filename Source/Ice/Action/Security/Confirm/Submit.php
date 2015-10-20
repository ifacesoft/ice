<?php

namespace Ice\Action;

use Ebs\Model\Token;
use Ice\Core\Logger;
use Ice\Core\Widget_Security;
use Ice\Helper\Date;

class Security_Confirm_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $logger = $securityForm->getLogger();

        try {
            $token = Token::createQueryBuilder()
                ->eq(['token' => $securityForm->getValue('token')])
                ->lt('/expired', Date::get())
                ->getSelectQuery('/data__json')
                ->getModel();

            if (!$token) {
                return [
                    'error' => $logger->info('Токен не наден или истек срок его действия', Logger::DANGER)
                ];
            }

            $this->confirm($token->get('/data'), $input);

            return array_merge(
                ['success' => $logger->info('Регистрация прошла успешно', Logger::SUCCESS)],
                parent::run($input)
            );
        } catch (\Exception $e) {
            return [
                'error' => $logger->info('Подтверждение не удалось', Logger::DANGER)
            ];
        }
    }

}