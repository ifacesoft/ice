<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.10.15
 * Time: 18:16
 */

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_RegisterConfirm_Submit;
use Ice\DataProvider\Request;

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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register confirmation', ['valueResource' => true])])
            ->text(
                'token',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => ['token' => ['providers' => Request::class]]
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
}