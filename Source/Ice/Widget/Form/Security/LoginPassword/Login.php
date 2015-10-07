<?php

namespace Ice\Widget;

use Ice\Action\Form_Submit;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;
use Ice\Core\Widget_Form_Security_Login;

class Form_Security_LoginPassword_Login extends Widget_Form_Security_Login
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'login' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
            'action' => [
                'class' => Form_Submit::getClass(),
                'params' => [],
                'url' => 'ice_security_login',
                'method' => 'POST',
                'callback' => null
            ]
        ];
    }

    protected function build(array $input)
    {
        $this
            ->text(
                'login',
                [
                    'label' => 'Login',
                    'required' => true,
                    'placeholder' => 'login_placeholder',
                    'validators' => ['Ice:Length_Min' => 2]
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
            ->button('signin', ['label' => 'Sign in', 'submit' => true]);

        return [];
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.1
     * @param array $widget
     * @return array|null
     * @throws \Ice\Core\Exception
     */
    protected function action(array $widget)
    {
        /** @var Model $accountModelClass */
        $accountModelClass = $this->getAccountModelClass();

        if (!$accountModelClass) {
            return $this->getLogger()
                ->exception(
                    ['Unknown accountModelClass', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        $values = $this->validate();

        /** @var Security_Account|Model $account */
        $account = $accountModelClass::createQueryBuilder()
            ->eq(['login' => $values['login']])
            ->limit(1)
            ->getSelectQuery(['password', '/expired', 'user__fk'])
            ->getModel();

        $result['account'] = $account && $this->verify($account, $values)
            ? $this->signIn($account)
            : $this->getLogger()
                ->exception(
                    ['Log in failure', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );

        return $result;
    }

    /**
     * Verify account
     *
     * @param Security_Account|Model $account
     * @param array $values
     * @return boolean
     */
    protected function verify(Security_Account $account, $values)
    {
        return md5($values['password']) === $account->get('password');
    }
}
