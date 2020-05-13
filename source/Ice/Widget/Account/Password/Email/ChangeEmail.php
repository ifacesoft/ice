<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_ChangeEmail_Submit;
use Ice\Core\Model_Account;
use Ice\DataProvider\Request;
use Ice\Exception\Error;

class Account_Password_Email_ChangeEmail extends Account_Form
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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Change Email', ['valueResource' => true])])
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
            ->divMessage()
            ->button(
                'change_email',
                [
                    'route' => 'ice_security_change_email_request',
                    'submit' => Security_Password_Email_ChangeEmail_Submit::class
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
        return null;
    }
}