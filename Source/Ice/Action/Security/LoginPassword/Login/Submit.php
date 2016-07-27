<?php

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Model\Log_Security;
use Ice\Widget\Security_LoginPassword_Login;
use Ice\Helper\Logger as Helper_Logger;

class Security_LoginPassword_Login_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $logger = $this->getLogger();

        /** @var Security_LoginPassword_Login $securityForm */
        $securityForm = $input['widget'];

        $accountModelClass = $securityForm->getAccountModelClass();

        if (!$accountModelClass) {
            return $securityForm->getLogger()
                ->exception(
                    ['Unknown accountModelClass', [], $securityForm->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'form_class' => get_class($securityForm)
        ]);

        try {
            $values = $securityForm->validate();

            /** @var Security_Account|Model $account */
            $account = $accountModelClass::createQueryBuilder()
                ->eq(['login' => $values['login']])
                ->limit(1)
                ->getSelectQuery(['/pk', 'password', '/expired', 'user__fk'])
                ->getModel();

            if (!$account) {
                $securityForm->getLogger()->exception(['Account with login {$0} not found', $values['login']], __FILE__, __LINE__);
            }

            if (!$account->securityVerify($values)) {
                $securityForm->getLogger()->exception('Authentication data is not valid. Please, check input.', __FILE__, __LINE__);
            }

            $log->set('account_key', $account->getPkValue());

            $this->signIn($account, $input, $log);

            return array_merge(
                parent::run($input),
                ['success' => $securityForm->getLogger()->info('Login successfully', Logger::SUCCESS, true)]
                
            );
        } catch (\Exception $e) {
            $log->set('error', Helper_Logger::getMessage($e));

            $logger->save($log);

            return ['error' => $securityForm->getLogger()->info($e->getMessage(), Logger::DANGER, true)];
        }
    }
}