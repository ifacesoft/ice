<?php

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Widget\Security_LoginPassword_Login;

class Security_LoginPassword_Login_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_LoginPassword_Login $form */
        $form = $input['widget'];

        $accountModelClass = $form->getAccountLoginPasswordModelClass();

        if (!$accountModelClass) {
            return $form->getLogger()
                ->exception(
                    ['Unknown accountModelClass', [], $form->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        try {
            $values = $form->validate();

            /** @var Security_Account|Model $account */
            $account = $accountModelClass::createQueryBuilder()
                ->eq(['login' => $values['login']])
                ->limit(1)
                ->getSelectQuery(['/pk', 'password', '/expired', 'user__fk'])
                ->getModel();

            if (!$account) {
                $form->getLogger()->exception(['Account with login {$0} not found', $values['login']], __FILE__, __LINE__);
            }

            if (!$account->securityVerify($values)) {
                $form->getLogger()->exception('Authentication data is not valid. Please, check input.', __FILE__, __LINE__);
            }

            $this->signIn($account, $input);

            return array_merge(
                parent::run($input),
                ['success' => $form->getLogger()->info('Login successfully', Logger::SUCCESS, true)]
                
            );
        } catch (\Exception $e) {
            return ['error' => $form->getLogger()->info($e->getMessage(), Logger::DANGER, true)];
        }
    }
}