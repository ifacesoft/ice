<?php

namespace Ice\Widget\Form\Security;

use Ice\Core\Model;
use Ice\Core\Security;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;
use Ice\Widget\Form\Simple;

class EmailPassword_Login extends Widget_Form_Security
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'email' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data)
            ->setResource(__CLASS__)
            ->setTemplate(Simple::class)
            ->text(
                'email',
                'Email',
                [
                    'required' => true,
                    'placeholder' => 'email_placeholder',
                    'validators' => 'Ice:Email'
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
            ->eq(['email' => $values['email']])
            ->limit(1)
            ->getSelectQuery(['password', '/expired', 'user__fk'])
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
        return password_verify($values['password'], $account->get('password'));
    }
}
