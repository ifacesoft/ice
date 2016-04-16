<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.10.15
 * Time: 18:16
 */

namespace Ice\Widget;

use Ice\Action\Security_LoginPassword_RegisterConfirm_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;

class Security_LoginPassword_RegisterConfirm extends Widget_Security
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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register confirmation')])
            ->text(
                'token',
                [
                    'placeholder' => 'token_placeholder',
                    'required' => true,
                ]
            )
            ->div('ice-message', ['label' => '&nbsp;', 'resource' => false])
            ->button(
                'register_confirm',
                [
                    'route' => 'ice_security_register_confirm_request',
                    'submit' => Security_LoginPassword_RegisterConfirm_Submit::class
                ]
            );
    }
}