<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_Register_Submit;
use Ice\Core\Widget_Security;

class Security_EmailPassword_Register extends Widget_Security
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'email' => ['providers' => 'request'],
                'password' => ['providers' => 'request'],
                'password1' => ['providers' => 'request']
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
            if ($key == 'password1') {
                [
                    $this->validateScheme['password1']['Ice:Equal'] = $this->getValue('password')
                ];
            }

            parent::bind([$key => $value]);
        }

        return $this;
    }

    protected function build(array $input)
    {
        $this
            ->text(
                'email',
                [
                    'label' => 'Email',
                    'required' => true,
                    'placeholder' => 'email_placeholder',
                    'validators' => 'Ice:Email'
                ]
            )
            ->password(
                'password',
                [
                    'label' => 'Password',
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->password(
                'password1',
                ['label' => 'Password1', 'placeholder' => 'password1_placeholder']
            )
            ->button(
                'register',
                [
                    'submit' => [
                        'action' => Security_EmailPassword_Register_Submit::class,
                        'url' => 'ice_security_register'
                    ]
                ]
            );

        return [];
    }
}
