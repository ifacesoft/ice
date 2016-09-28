<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_RestorePasswordConfirm_Submit;
use Ice\DataProvider\Request;

class Account_Password_Email_RestorePasswordConfirm extends Account_Form
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password confirmation', ['valueResource' => true])])
            ->text(
                'token',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => ['token' => ['providers' => Request::class]]
                ]
            )
            ->password(
                'new_password',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => ['new_password' => ['providers' => Request::class]]
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
                                    'value' => $this->get('new_password'),
                                    'message' => 'Passwords must be equals'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'restore_password_confirm',
                [
                    'route' => 'ice_security_restore_password_confirm_request',
                    'submit' => Security_Password_Email_RestorePasswordConfirm_Submit::class
                ]
            );
    }
}