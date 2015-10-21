<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Widget_Security;

class Security_EmailPassword_RestoreRequest_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $logger = $securityForm->getLogger();

        try {
            /** @var Model $accountModelClass */
            $accountModelClass = $securityForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $securityForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            $account = $accountModelClass::getSelectQuery('/pk', ['email' => $securityForm->getValue('email')])->getModel();

            if (!$account) {
                return [
                    'error' => $logger->info('Пользователь с таким электронным адресом не найден.', Logger::DANGER)
                ];
            }

            $this->restoreRequest($account, $input);

            return array_merge(
                ['success' => $logger->info('Ключ для восстановления пароля отправлен вам на электронный адрес', Logger::SUCCESS)],
                parent::run($input)
            );

        } catch (\Exception $e) {
            $logger->error('Запрос на восстановление пароля не удался', __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Запрос на восстановление пароля не удался', Logger::DANGER)
            ];
        }
    }

}