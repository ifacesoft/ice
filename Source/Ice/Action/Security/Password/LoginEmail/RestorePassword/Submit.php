<?php

namespace Ice\Action;

use Ice\Validator\Email;
use Ice\Widget\Account_Password_Login_RestorePassword;
use Ice\Widget\Account_Password_Email_RestorePassword;

class Security_Password_LoginEmail_RestorePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $error = '';

        $widget = $input['widget'];

        $isEmail = Email::getInstance()->validate($widget->get(), 'username', []);

        if ($isEmail) {
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

            $error .= $output['error'];
        }

        $accountLoginPasswordSubmitClass = $widget->getAccountLoginPasswordSubmitClass();

        $output = $accountLoginPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => Account_Password_Login_RestorePassword::getInstance($widget->getInstanceKey())
                ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                ->set([
                    'login' => $widget->get('username'),
                ])
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return ['error' => $isEmail ? $error : $output['error']];
    }
}