<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_RestoreConfirm_Submit;
use Ice\Action\Security_EmailPassword_RestorePasswordConfirm_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;

class Security_EmailPassword_RestorePasswordConfirm extends Widget_Security
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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['token' => ['providers' => [Request::class, 'default', Router::class]]],
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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password confirmation')])
            ->text(
                'token',
                [
                    'placeholder' => 'token_placeholder',
                    'required' => true,
                ]
            )
            ->password(
                'new_password',
                [
                    'placeholder' => 'new_password_placeholder',
                    'required' => true,
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => 'confirm_password_placeholder',
                    'required' => true,
                ]
            )
            ->div('ice-message', ['label' => '&nbsp;'])
            ->button(
                'restore_password_confirm',
                [
                    'submit' => [
                        'action' => Security_EmailPassword_RestorePasswordConfirm_Submit::class,
                        'url' => 'ice_security_restore_password_confirm_request'
                    ]
                ]
            );
    }
}