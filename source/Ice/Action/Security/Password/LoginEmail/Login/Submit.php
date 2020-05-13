<?php

namespace Ice\Action;

use Ice\Core\Exception;
use Ice\Exception\Config_Error;
use Ice\Exception\Console_Run;
use Ice\Exception\Http;
use Ice\Exception\Http_Redirect;
use Ice\Validator\Email;
use Ice\Widget\Account_Password_Email_Login;
use Ice\Widget\Account_Password_Login_Login;
use Ice\Widget\Account_Password_LoginEmail_Login;

class Security_Password_LoginEmail_Login_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     * @throws Exception
     * @throws Config_Error
     * @throws Console_Run
     * @throws Http
     * @throws Http_Redirect
     */
    public function run(array $input)
    {
        /** @var Account_Password_LoginEmail_Login $widget */
        $widget = $input['widget'];

        if (Email::getInstance()->validate($widget->get(), 'username', [])) {
            $output = Security_SignIn::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Email_Login::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                    ->setProlongate($widget->getProlongate())// todo: так же надо прокинуть остальные свойства (redirect, timeout etc.)
                    ->set([
                        'email' => $widget->get('username'),
                        'password' => $widget->get('password')
                    ])
            ]);

            if (!isset($output['error']) || (isset($output['exception']) && $output['exception'] instanceof \Exception)) {
                return $output;
            }
        }

        return Security_SignIn::call([
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