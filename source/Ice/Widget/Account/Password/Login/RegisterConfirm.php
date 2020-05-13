<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Login_RegisterConfirm_Submit;
use Ice\Core\Model_Account;
use Ice\DataProvider\Request;

class Account_Password_Login_RegisterConfirm extends Account_Form
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
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'token' => [
                            'providers' => [Request::class, 'default'],
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'register_confirm',
                [
                    'route' => 'ice_security_register_confirm_request',
                    'submit' => Security_Password_Login_RegisterConfirm_Submit::class
                ]
            );
    }

    /**
     * @return Model_Account
     */
    public function getAccount()
    {
        return null;
    }
}