<?php

namespace Ice\Widget;

use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;
use Ice\Core\Widget_Form_Security_Register;
use Ice\Widget\Form;
use Ice\Widget\Form\Simple;

class Form_Security_EmailPassword_Register extends Widget_Form_Security_Register
{
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null],
            'input' => [
                'email' => ['providers' => 'request'],
                'password' => ['providers' => 'request'],
                'password1' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create()
    {
        return parent::create()
            ->setResource(__CLASS__)
            ->setTemplate(Form::getClass())
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
            ->password(
                'password1',
                ['label' => 'Password1', 'placeholder' => 'password1_placeholder']
            )
            ->button('register', ['label' => 'Sign up', 'onclick' => 'POST']);
    }

    /**
     * @param array $params
     * @return EmailPassword_Register
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'password1') {
                [
                    $this->validateScheme['password1']['Ice:Equal'] = $this->getValue('password')
                ];
            }

            parent::bind([$key => $value]);
        }

        return $this;
    }

    /**
     * Register
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @param array $userData
     * @param null $dataSource
     * @return Security_Account
     * @throws \Ice\Core\Exception
     * @version 1.1
     * @since   0.1
     */
    public function register(array $userData = [], $dataSource = null)
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

        $accountData = $this->validate();

        $accountData['password'] = password_hash($accountData['password'], PASSWORD_DEFAULT);

        return $this->signUp($accountModelClass, $accountData, $userData, $dataSource);
    }
}
