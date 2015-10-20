<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Widget\Security_EmailPassword_Login;

class Security_EmailPassword_Login_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_EmailPassword_Login $form */
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

        $values = $form->validate();

        /** @var Security_Account|Model $account */
        $account = $accountModelClass::createQueryBuilder()
            ->eq(['email' => $values['email']])
            ->limit(1)
            ->getSelectQuery(['password', '/expired', 'user__fk'])
            ->getModel();

        if (!$account && !$form->verify($account, $values)) {
            $form->getLogger()->exception(['Log in failure', [], $form->getResource()], __FILE__, __LINE__);
        }

        $this->signIn($account, $input);

        return array_merge(
            ['success' => $form->getLogger()->info('Login successfully', Logger::SUCCESS)],
            parent::run($input)
        );
    }
}