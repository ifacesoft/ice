<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_Restore_Submit;
use Ice\Action\Security_EmailPassword_RestorePassword_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_EmailPassword_RestorePassword extends Widget_Security
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
            ->divMessage()
            ->button(
                'restore_password',
                [
                    'route' => 'ice_security_restore_password_request',
                    'submit' => Security_EmailPassword_RestorePassword_Submit::class
                ]
            );

        return [];
    }

}