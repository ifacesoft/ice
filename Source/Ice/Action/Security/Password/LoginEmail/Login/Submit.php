<?php
namespace Ice\Action;

use Ice\Validator\Email;
use Ice\Widget\Account_Password_Email_Login;
use Ice\Widget\Account_Password_LoginEmail_Login;
use Ice\Widget\Account_Password_Login_Login;

class Security_Password_LoginEmail_Login_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Account_Password_LoginEmail_Login $widget */
        $widget = $input['widget'];

        if (Email::getInstance()->validate($widget->get(), 'username', [])) {
            return Security_Password_Email_Login_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Email_Login::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                    ->setProlongate($widget->getProlongate())// todo: так же надо прокинуть остальные свойства (redirect, timeout etc.)
                    ->set([
                        'email' => $widget->get('username'),
                        'password' => $widget->get('password')
                    ])
            ]);
        } else {
            return Security_Password_Login_Login_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Login_Login::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                    ->setProlongate($widget->getProlongate())// todo: так же надо прокинуть остальные свойства (redirect, timeout etc.)
                    ->set([
                        'login' => $widget->get('username'),
                        'password' => $widget->get('password')
                    ])
            ]);
        }
    }
}