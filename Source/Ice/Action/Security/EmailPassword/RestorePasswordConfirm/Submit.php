<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Core\Widget_Security;
use Ice\Helper\Date;
use Ice\Model\Token;
use Ice\Core\Security as Core_Security;

class Security_EmailPassword_RestorePasswordConfirm_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $logger = $securityForm->getLogger();

        try {
            $values = $securityForm->validate();
            
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

            $accountModelClass = $securityForm->getAccountEmailPasswordModelClass();
            $accountModelClassName = $accountModelClass::getClassName();
            
            /** @var Security_Account|Model $account */
            $account = $accountModelClass::createQueryBuilder()
                ->inner(Token::class, '/pk', 'Token.id=' . $accountModelClassName . '.token_id AND Token.token="' . $token->get('token') . '"')
                ->getSelectQuery(['password', '/expired', 'user__fk'])
                ->getModel();
            if (!$account) {
                $securityForm->getLogger()->exception('Account not found', __FILE__, __LINE__);
            }
            
            $accountData = ['password' => $account->securityHash($values)];

            $this->changePassword($account, $accountData, $input);

            $token->remove();

            return array_merge(
                ['success' => $logger->info('Восстановление пароля прошло успешно', Logger::SUCCESS, true)],
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