<?php

namespace Ice\Action;

use Ebs\Model\Account_Login_Password;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Widget\Account_Form;

class Security_Password_Login_RestorePassword_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Account_Login_Password $accountModelClass */
            $accountModelClass = $accountForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $accountForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            /** @var Account_Login_Password $account */
            $account = $accountModelClass::getAccountByLogin($accountForm->get('login'));

            if (!$account) {
                return [
                    'error' => $logger->info('Пользователь с таким логином не найден.', Logger::DANGER, true)
                ];
            }

            $this->restorePassword($account, $input);

            return array_merge(
                ['success' => $logger->info('Ключ для восстановления пароля отправлен вам на электронный адрес', Logger::SUCCESS, true)],
                parent::run($input)
            );

        } catch (\Exception $e) {
            $logger->error($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        }
    }
}