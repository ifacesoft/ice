<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_RegisterConfirm_Submit;
use Ice\DataProvider\Request;
use Ice\DataProvider\Request_Http_Raw;

class Account_Password_Email_RegisterConfirm extends Account_Form
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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
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
     * @throws \Ice\Core\Exception
     */
    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register confirmation', ['valueResource' => true])])
            ->text(
                'token',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => [
                        'token' => [
                            'providers' => [Request_Http_Raw::class, Request::class, 'default'],
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'register_confirm',
                [
                    'route' => 'ice_security_register_confirm_request',
                    'submit' => Security_Password_Email_RegisterConfirm_Submit::class,
                ]
            );
    }

    public function getAccount()
    {
        return null;
    }
}