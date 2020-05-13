<?php

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Widget\Account_Password_Email_RestorePasswordConfirm;
use Ice\Widget\Account_Password_Login_RestorePasswordConfirm;

class Security_Password_LoginEmail_RestorePasswordConfirm_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     * @throws \Ice\Core\Exception
     */
    public function run(array $input)
    {
        $widget = $input['widget'];

        $accountEmailPasswordSubmitClass = $widget->getAccountEmailPasswordSubmitClass();

        $output = $accountEmailPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => Account_Password_Email_RestorePasswordConfirm::getInstance($widget->getInstanceKey())
                ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                ->setPart('token', $widget->getPart('token'))
                ->setPart('new_password', $widget->getPart('new_password'))
                ->setPart('confirm_password', $widget->getPart('confirm_password'))
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        $error = $output['error'];

        $accountLoginPasswordSubmitClass = $widget->getAccountLoginPasswordSubmitClass();

        $output = $accountLoginPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => Account_Password_Login_RestorePasswordConfirm::getInstance($widget->getInstanceKey())
                ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                ->setPart('token', $widget->getPart('token'))
                ->setPart('new_password', $widget->getPart('new_password'))
                ->setPart('confirm_password', $widget->getPart('confirm_password'))
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return ['error' => $error];
    }
}