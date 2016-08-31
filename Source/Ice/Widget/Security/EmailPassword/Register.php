<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_Register_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_EmailPassword_Register extends Widget_Security
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
            ->password(
                'confirm_password',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => [
                        'confirm_password' => [
                            'providers' => Request::class,
                            'validators' => [
                                'Ice:Equal' => [
                                    'value' => $this->getPart('password')->get('password'),
                                    'message' => 'Passwords must be equals'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'register',
                [
                    'route' => 'ice_security_register_request',
                    'submit' => Security_EmailPassword_Register_Submit::class
                ]
            );

        return [];
    }
}
