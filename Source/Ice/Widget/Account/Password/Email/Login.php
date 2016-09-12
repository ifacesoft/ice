<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_Login_Submit;
use Ice\DataProvider\Request;

class Account_Password_Email_Login extends Account_Form
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
        ];
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Login')])
            ->text(
                'email',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'email' => [
                            'providers' => Request::class,
                            'validators' => 'Ice:Email'
                        ]
                    ]
                ]
            )
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'password' => [
                            'providers' => Request::class,
                            'validators' => ['Ice:Length_Min' => 5]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'login',
                [
                    'route' => 'ice_security_login_request',
                    'submit' => Security_Password_Email_Login_Submit::class
                ]
            );

        return [];
    }
}
