<?php

namespace Ice\Widget;

use Ice\Action\Security_LoginPassword_Register_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_LoginPassword_Register extends Widget_Security
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'login' => ['providers' => Request::class],
                'password' => ['providers' => Request::class],
                'confirm_password' => ['providers' => Request::class]
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
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
                        'value' => $this->getValue('password'),
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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register')])
            ->text(
                'login',
                [
                    'required' => true,
                    'placeholder' => 'login_placeholder',
                    'validators' => ['Ice:Length_Min' => 3]
                ]
            )
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => 'confirm_password_placeholder'
                ]
            )
            ->div('ice-message', ['label' => '&nbsp;'])
            ->button(
                'register',
                [
                    'submit' => [
                        'action' => Security_LoginPassword_Register_Submit::class,
                        'url' => 'ice_security_register_request'
                    ]
                ]
            );

        return [];
    }
}
