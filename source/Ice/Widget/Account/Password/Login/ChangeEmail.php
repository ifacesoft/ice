<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Login_ChangeEmail_Submit;
use Ice\Core\Model_Account;
use Ice\DataProvider\Request;
use Ice\Exception\Error;

class Account_Password_Login_ChangeEmail extends Account_Form
{
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

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password', ['valueResource' => true])])
            ->text(
                'email',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'email' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => ['Ice:Length_Min' => 2]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'change_email',
                [
                    'route' => 'ice_security_change_email_password_request',
                    'submit' => Security_Password_Login_ChangeEmail_Submit::class
                ]
            );

        return [];
    }

    /**
     * @return void
     * @throws Error
     */
    public function getAccount()
    {
        throw new Error('Method do not call');
    }
}