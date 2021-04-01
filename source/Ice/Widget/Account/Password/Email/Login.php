<?php

namespace Ice\Widget;

use Ice\Exception\Security_Account_EmailNotConfirmed;
use Ice\Model\User;
use Ice\Action\Security_SignIn;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\DataProvider\Request;

class Account_Password_Email_Login extends Account_Form
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

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Login')])
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

    /**
     * @return Model|\Ice\Core\Model_Account|null
     * @throws \Ice\Core\Exception
     * @throws \Exception
     */
    public function getAccount()
    {
        /** @var Model $accountClass */
        $accountClass = $this->getAccountModelClass();

        $email = $this->get('email');

        $account = $accountClass::createQueryBuilder()
            ->inner(User::class)
            ->eq(['email' => $email])
            ->eq(['email_canonical' => mb_strtolower($email)], User::class, QueryBuilder::SQL_LOGICAL_OR)
            ->getSelectQuery('*')
            ->getModel();
        
        if ($account && !$account->get('email_confirmed', 0)) {
            throw new Security_Account_EmailNotConfirmed('Электронная почта не подтверждена');
        }

        return $account;
    }
}
