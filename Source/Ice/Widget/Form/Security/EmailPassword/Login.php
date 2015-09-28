<?php

namespace Ice\Widget;

use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Widget\Form;
use Ice\Widget\Form\Simple;

class Form_Security_EmailPassword_Login extends Widget_Form_Security_Login
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null],
            'input' => [
                'email' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    protected function build(array $input)
    {
        parent::build($input);

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
        ->button('submit', ['label' => 'Sign in', 'onclick' => 'POST']);
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.1
     * @param $token
     * @return array|null|string
     * @throws \Ice\Core\Exception
     */
    public function action($token)
    {
        $result = parent::action($token);

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

        $result['account'] = $account && $this->verify($account, $values)
            ? $this->signIn($account)
            : Widget_Form_Security::getLogger()
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
        return password_verify($values['password'], $account->get('password'));
    }
}
