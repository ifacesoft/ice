<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Model_Account;
use Ice\Helper\Date;
use Ice\Model\Account;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

class Security_Password_Login_RestorePasswordConfirm_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            $values = $accountForm->validate();

            /** @var Token $token */
            $token = Token::createQueryBuilder()
                ->eq(['/' => $accountForm->getPart('token')->get('token')])
                ->gt('/expired', Date::get())
                ->getSelectQuery('*')
                ->getModel();

            if (!$token) {
                return [
                    'error' => $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true)
                ];
            }

            $accountModelClass = $accountForm->getAccountLoginPasswordModelClass();
            $accountModelClassName = $accountModelClass::getClassName();

            /** @var Model_Account $account */
            $account = $accountModelClass::createQueryBuilder()
                ->inner(Token::class, '/pk', 'Token.id=' . $accountModelClassName . '.token_id AND Token.token="' . $token->get('token') . '"')
                ->getSelectQuery(['/pk', 'password', '/expired', 'user__fk'])
                ->getModel();
            if (!$account) {
                $accountForm->getLogger()->exception('Account not found', __FILE__, __LINE__);
            }

            $accountData = ['password' => $account->securityHash($values, 'new_password')];

            $this->changePassword($account, $accountData, $input);

            $account->set(['token__fk' => null])->save();
            $token->remove();

            return array_merge(
                ['success' => $logger->info('Восстановление пароля прошло успешно', Logger::SUCCESS, true)],
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