<?php

namespace Ice\Widget;

use Ice\Action\Security_LoginPassword_Register_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_LoginPassword_Register extends Widget_Security
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register')])
            ->text(
                'login',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'login' => [
                            'providers' => Request::class,
                            'validators' => ['Ice:Length_Min' => 3]
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
            ->password(
                'confirm_password',
                [
                    'placeholder' => true,
                    'params' => [
                        'confirm_password' => [
                            'providers' => Request::class,
                            'validators' => [
                                'Ice:Equal' => [
                                    'value' => $this->get('password'),
                                    'message' => 'Passwords must be equals'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->div('ice-message', ['value' => '&nbsp;', 'encode' => false, 'resource' => false])
            ->button(
                'register',
                [
                    'route' => 'ice_security_register_request',
                    'submit' => Security_LoginPassword_Register_Submit::class
                ]
            );

        return [];
    }
}
