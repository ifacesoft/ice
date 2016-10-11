<?php
namespace Ice\Action;

use Ice\Validator\Email;
use Ice\Widget\Account_Password_Email_ChangePassword;
use Ice\Widget\Account_Password_Login_ChangePassword;
use Ice\Core\Security as Core_Security;

class Security_Password_LoginEmail_ChangePassword_Submit extends Security
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

        $login = Core_Security::getInstance()->getUser()->get('/login');

        if (Email::getInstance()->validate(['login' => $login], 'login', [])) {
            $output = Security_Password_Email_ChangePassword_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Email_ChangePassword::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                    ->set([
                        'password' => $widget->get('password'),
                        'new_password' => $widget->get('new_password'),
                        'confirm_password' => $widget->get('confirm_password')
                    ])
            ]);

            if (!isset($output['error'])) {
                return $output;
            }

            $error .= $output['error'];
        }

        $output = Security_Password_Login_ChangePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => Account_Password_Login_ChangePassword::getInstance($widget->getInstanceKey())
                ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                ->set([
                    'password' => $widget->get('password'),
                    'new_password' => $widget->get('new_password'),
                    'confirm_password' => $widget->get('confirm_password')
                ])
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return ['error' => $error . ' ' . $output['error']];
    }
}