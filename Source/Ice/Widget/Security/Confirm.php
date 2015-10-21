<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.10.15
 * Time: 18:16
 */

namespace Ice\Widget;


use Ice\Action\Security_Confirm_Submit;
use Ice\Core\Widget_Security;

class Security_Confirm extends Widget_Security
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['token' => ['providers' => ['default', 'request', 'router']]],
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
            ->setRedirect('ebs_security_register', 2000)
            ->addClasses('lan-buttons')
            ->setHorizontal(5)
            ->text('token')
            ->div('ice-message', ['label' => '&nbsp;'])
            ->button(
                'confirm',
                [
                    'classes' => 'pull-right btn-primary',
                    'submit' => [
                        'action' => Security_Confirm_Submit::class,
                        'url' => 'ice_ajax'
                    ]
                ]
            );
    }
}