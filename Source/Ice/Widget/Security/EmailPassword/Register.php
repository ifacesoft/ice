<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_Register_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_EmailPassword_Register extends Widget_Security
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [],
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
                'email',
                [
                    'required' => true,
                    'placeholder' => 'email_placeholder',
                    'validators' => 'Ice:Email',
                    'providers' => Request::class
                ]
            )
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
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
                'register',
                [
                    'route' => 'ice_security_register_request',
                    'submit' => Security_EmailPassword_Register_Submit::class
                ]
            );

        return [];
    }
}
