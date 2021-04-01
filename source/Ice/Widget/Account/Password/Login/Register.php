<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Login_Register_Submit;
use Ice\Core\Model;
use Ice\DataProvider\Request;

class Account_Password_Login_Register extends Account_Form
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

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register')])
            ->text(
                'login',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'login' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => ['Ice:Length_Min' => 3]
                        ]
                    ]
                ]
            )
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'password' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => ['Ice:Length_Min' => 5]
                        ]
                    ]
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => true,
                    'params' => [
                        'confirm_password' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => [
                                'Ice:Equal' => [
                                    'value' => $this->get('password'),
                                    'message' => 'Passwords must be equals'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'register',
                [
                    'route' => 'ice_security_register_request',
                    'submit' => Security_Password_Login_Register_Submit::class
                ]
            );

        return [];
    }

    public function getAccount()
    {
        /** @var Model $accountClass */
        $accountClass = $this->getAccountModelClass();

        return $accountClass::createQueryBuilder()
            ->eq(['login' => $this->get('login')])
            ->getSelectQuery('*')
            ->getModel();
    }
}
