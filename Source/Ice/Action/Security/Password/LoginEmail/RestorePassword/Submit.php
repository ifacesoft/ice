<?php

namespace Ice\Action;

use Ice\Validator\Email;
use Ice\Widget\Account_Password_Login_RestorePassword;
use Ice\Widget\Account_Password_Email_RestorePassword;
use Ice\Widget\Account_Password_LoginEmail_RestorePassword;

class Security_Password_LoginEmail_RestorePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $widget = $input['widget'];

        if (Email::getInstance()->validate($widget->get(), 'username', [])) {
            $accountEmailPasswordSubmitClass = $widget->getAccountEmailPasswordSubmitClass();

            $output = $accountEmailPasswordSubmitClass::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Email_RestorePassword::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                    ->set([
                        'email' => $widget->get('username'),
                    ])
            ]);

            if (!isset($output['error'])) {
                return $output;
            }
        }

        $accountLoginPasswordSubmitClass = $widget->getAccountLoginPasswordSubmitClass();

        return $accountLoginPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => Account_Password_Login_RestorePassword::getInstance($widget->getInstanceKey())
                ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                ->set([
                    'login' => $widget->get('username'),
                ])
        ]);
    }
}