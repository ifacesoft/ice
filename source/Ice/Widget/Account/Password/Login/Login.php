<?php

namespace Ice\Widget;

use Ice\Model\Account_Login_Password;
use Ice\Action\Security_SignIn;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\DataProvider\Request;
use Ice\Model\User;

class Account_Password_Login_Login extends Account_Form
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
        ];
    }

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->setAccountModelClass(Account_Login_Password::class);
    }

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Login')])
            ->text(
                'login',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'login' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => ['Ice:Length_Min' => 2]
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
            ->divMessage()
            ->button(
                'submit',
                [
                    'route' => 'ice_security_login_request',
                    'submit' => Security_SignIn::class
                ]
            );

        return [];
    }

    public function getAccount()
    {
        /** @var Model $accountClass */
        $accountClass = $this->getAccountModelClass();

        $login = $this->get('login');

        return $accountClass::createQueryBuilder()
            ->inner(User::class)
            ->eq(['login' => $login])
            ->eq(['email_canonical' => mb_strtolower($login)], User::class, QueryBuilder::SQL_LOGICAL_OR)
            ->getSelectQuery('*')
            ->getModel();
    }
}
