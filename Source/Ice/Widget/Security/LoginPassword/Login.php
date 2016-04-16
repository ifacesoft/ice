<?php

namespace Ice\Widget;

use Ice\Action\Security_LoginPassword_Login_Submit;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_LoginPassword_Login extends Widget_Security
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'login' => ['providers' => Request::class],
                'password' => ['providers' => Request::class]
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
        ];
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Login')])
            ->text(
                'login',
                [
                    'required' => true,
                    'placeholder' => 'login_placeholder',
                    'validators' => ['Ice:Length_Min' => 2]
                ]
            )
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->div('ice-message', ['label' => '&nbsp;', 'resource' => false])
            ->button(
                'login',
                [
                    'route' => 'ice_security_login_request',
                    'submit' => Security_LoginPassword_Login_Submit::class
                ]
            );

        return [];
    }
}
