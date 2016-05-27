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
                    'placeholder' => 'token_placeholder',
                    'required' => true,
                    'providers' => Request::class
                ]
            )
            ->password(
                'new_password',
                [
                    'placeholder' => 'new_password_placeholder',
                    'required' => true,
                    'providers' => Request::class
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => 'confirm_password_placeholder',
                    'required' => true,
                    'providers' => Request::class
                ]
            )
            ->div('ice-message', ['value' => '&nbsp;', 'encode' => false, 'resource' => false])
            ->button(
                'restore_password_confirm',
                [
                    'route' => 'ice_security_restore_password_confirm_request',
                    'submit' => Security_EmailPassword_RestorePasswordConfirm_Submit::class
                ]
            );
    }
}