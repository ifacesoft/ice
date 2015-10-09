<?php

namespace Ice\Widget;

use Ice\Action\Form_Submit;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;
use Ice\Core\Widget_Form_Security_Login;

class Form_Security_EmailPassword_Login extends Widget_Form_Security_Login
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'email' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
            'action' => [
                'class' => Form_Submit::getClass(),
                'params' => [],
                'url' => 'ice_security_login',
                'method' => 'POST',
                'callback' => null
            ]
        ];
    }

    protected function build(array $input)
    {
        $this
            ->text(
                'email',
                [
                    'label' => 'Email',
                    'required' => true,
                    'placeholder' => 'email_placeholder',
                    'validators' => 'Ice:Email'
                ]
            )
            ->password(
                'password',
                [
                    'label' => 'Password',
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->button('signin', ['label' => 'Sign in', 'submit' => true]);

        return [];
    }

    /**
     * Verify account
     *
     * @param Security_Account|Model $account
     * @param array $values
     * @return boolean
     */
    public function verify(Security_Account $account, $values)
    {
        return password_verify($values['password'], $account->get('password'));
    }
}
