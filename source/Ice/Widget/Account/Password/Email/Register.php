<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_Register_Submit;
use Ice\Core\Model;
use Ice\DataProvider\Request;

class Account_Password_Email_Register extends Account_Form
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = Form::class;
        $config['render']['resource'] = true;

        return $config;
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Register')])
            ->text(
                'email',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'email' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => 'Ice:Email'
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
                    'required' => true,
                    'params' => [
                        'confirm_password' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => [
                                'Ice:Equal' => [
                                    'name' =>'password',
                                    'message' => 'Passwords not match'
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
                    'submit' => Security_Password_Email_Register_Submit::class
                ]
            );

        return [];
    }

    public function getAccount()
    {
        /** @var Model $accountClass */
        $accountClass = $this->getAccountModelClass();

        return $accountClass::createQueryBuilder()
            ->eq(['email' => $this->get('email')])
            ->getSelectQuery('*')
            ->getModel();
    }
}
