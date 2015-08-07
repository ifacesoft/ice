<?php

namespace Ice\Widget\Form\Security;

use Ice\Core\Model;
use Ice\Core\Security;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;
use Ice\Widget\Form\Simple;

class LoginPassword_Login extends Widget_Form_Security
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'login' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data)
            ->setTemplate(Simple::class)
            ->text(
                'login',
                'Login',
                [
                    'required' => true,
                    'placeholder' => 'login_placeholder',
                    'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
                ]
            )
            ->password(
                'password',
                'Password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->button('submit', 'Sign in', ['onclick' => 'POST']);
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.1
     */
    public function login()
    {
        /** @var Model $accountModelClass */
        $accountModelClass = $this->getAccountModelClass();

        if (!$accountModelClass) {
            return Widget_Form_Security::getLogger()
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
            ->getSelectQuery(['password', '/active', '/expired', 'user__fk'])
            ->getModel();

        return $this->verify($account, $values)
            ? Security::getInstance()->login($this->authenticate($account))
            : Widget_Form_Security::getLogger()
                ->exception(
                    ['Log in failure', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
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
        return md5($values['password']) == $account->get('password');
    }
}
