<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model_Account;
use Ice\Core\Security as Core_Security;
use Ice\Model\Account;
use Ice\Widget\Account_Password_Login_ChangePassword;

class Security_Password_Login_ChangePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_Password_Login_ChangePassword $form */
        $form = $input['widget'];

        $accountModelClass = $form->getAccountModelClass();

        if (!$accountModelClass) {
            return $form->getLogger()
                ->exception(
                    ['Unknown accountModelClass', [], $form->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        try {
            $values = $form->validate(); // deprecated// used in loginVerify

            /** @var Model_Account $account */
            $account = $accountModelClass::createQueryBuilder()
                ->eq(['user' => Core_Security::getInstance()->getUser()])
                ->limit(1)
                ->getSelectQuery(['/pk', 'password', 'user__fk'])
                ->getModel();

            if (!$account) {
                $form->getLogger()->exception('Account not found', __FILE__, __LINE__);
            }

            if (!$account->loginVerify($form)) {
                $form->getLogger()->exception('Authentication data is not valid. Please, check input.', __FILE__, __LINE__);
            }

            $accountData = ['password' => $account->getSecurityHash($values, 'new_password')];

            $this->changePassword($account, $accountData, $input);

            return array_merge(
                ['success' => $form->getLogger()->info('Change password successfully', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (\Exception $e) {
            return ['error' => $form->getLogger()->info($e->getMessage(), Logger::DANGER, true)];
        }
    }
}