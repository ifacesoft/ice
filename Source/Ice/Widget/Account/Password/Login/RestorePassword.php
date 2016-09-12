<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Login_RestorePassword_Submit;
use Ice\DataProvider\Request;

class Account_Password_Login_RestorePassword extends Account_Form
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password', ['valueResource' => true])])
            ->text(
                'login',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'login' => [
                            'providers' => Request::class,
                            'validators' => ['Ice:Length_Min' => 2]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'restore_password',
                [
                    'route' => 'ice_security_restore_password_request',
                    'submit' => Security_Password_Login_RestorePassword_Submit::class
                ]
            );

        return [];
    }

}