<?php

namespace Ice\Action;

use Ebs\Model\Account_Email_Password;
use Ice\Core\Logger;
use Ice\Widget\Account_Form;

class Security_Password_Email_RestorePassword_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Account_Email_Password $accountModelClass */
            $accountModelClass = $accountForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $accountForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            /** @var Account_Email_Password $account */
            $account = $accountModelClass::getSelectQuery(
                ['/pk', '/expired', 'token__fk'],
                ['email' => $accountForm->get('email')]
            )->getModel();

            if (!$account) {
                return [
                    'error' => $logger->info('Пользователь с таким электронным адресом не найден.', Logger::DANGER, true)
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