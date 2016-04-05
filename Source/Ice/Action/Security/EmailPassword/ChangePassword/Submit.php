<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Widget\Security_EmailPassword_ChangePassword;
use Ice\Core\Security as Core_Security;

class Security_EmailPassword_ChangePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_EmailPassword_ChangePassword $form */
        $form = $input['widget'];

        $accountModelClass = $form->getAccountEmailPasswordModelClass();

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
                ->eq(['user' => Core_Security::getInstance()->getUser()])
                ->limit(1)
                ->getSelectQuery(['/pk', 'password', '/expired', 'user__fk'])
                ->getModel();

            if (!$account) {
                $form->getLogger()->exception('Account not found', __FILE__, __LINE__);
            }

            if (!$account->securityVerify($values)) {
                $form->getLogger()->exception('Authentication data is not valid. Please, check input.', __FILE__, __LINE__);
            }

            $accountData = ['password' => $account->securityHash($values)];

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