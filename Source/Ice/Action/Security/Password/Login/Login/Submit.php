<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Model\Account;
use Ice\Widget\Account_Password_Login_Login;

class Security_Password_Login_Login_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Account_Password_Login_Login $securityForm */
        $securityForm = $input['widget'];

        $logger = $securityForm->getLogger();

        $accountModelClass = $securityForm->getAccountModelClass();

        if (!$accountModelClass) {
            return $logger->exception('Unknown accountModelClass', __FILE__, __LINE__);
        }

        try {
            /** @var Account $account */
            $account = $accountModelClass::createQueryBuilder()
                ->eq(['login' => $securityForm->get('login')])
                ->limit(1)
                ->getSelectQuery(['/pk', 'password', '/expired', 'user__fk'])
                ->getModel();

            if (!$account) {
                $logger->exception(['Account with login {$0} not found', $securityForm->get('login')], __FILE__, __LINE__);
            }

            $this->signIn($account, $securityForm);

            return array_merge(
                parent::run($input),
                ['success' => $logger->info('Login successfully', Logger::SUCCESS, true)]

            );
        } catch (\Exception $e) {
            return ['error' => $logger->info($e->getMessage(), Logger::DANGER, true)];
        }
    }
}