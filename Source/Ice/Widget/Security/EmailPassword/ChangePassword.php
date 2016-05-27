<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_ChangePassword_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_EmailPassword_ChangePassword extends Widget_Security
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

    /**
     * @param array $params
     * @return $this
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'confirm_password') {
                [
                    $this->validateScheme['confirm_password']['Ice:Equal'] = [
                        'value' => $this->getValue('new_password'),
                        'message' => 'Passwords must be equals'
                    ]
                ];
            }

            parent::bind([$key => $value]);
        }

        return $this;
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Change password', ['valueResource' => true])])
            ->text(
                'password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                    'providers' => Request::class
                ]
            )
            ->password(
                'new_password',
                [
                    'required' => true,
                    'placeholder' => 'new_password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
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
                'change_password',
                [
                    'route' => 'ice_security_change_password_request',
                    'submit' => Security_EmailPassword_ChangePassword_Submit::class
                ]
            );

        return [];
    }
}