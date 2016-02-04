<?php
namespace Ice\Widget;

use Ice\Action\Security_LoginEmailPassword_Login_Submit;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Data\Provider\Request;

class Security_LoginEmailPassword_Login extends Widget_Form_Security_Login
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;

    /**
     * @return null
     */
    public function getAccountLoginPasswordModelClass()
    {
        return $this->accountLoginPasswordModelClass;
    }

    /**
     * @return null
     */
    public function getAccountEmailPasswordModelClass()
    {
        return $this->accountEmailPasswordModelClass;
    }

    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'username' => ['providers' => Request::class],
                'password' => ['providers' => Request::class]
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
        ];
    }

    protected function build(array $input)
    {
        $this
            ->text(
                'username',
                [
                    'label' => 'Username',
                    'required' => true,
                    'placeholder' => 'username_placeholder',
                    'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers'],
                    'srOnly' => true,
                    'resetFormClass' => true
                ]
            )
            ->password(
                'password',
                [
                    'label' => 'Password',
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                    'srOnly' => true,
                    'resetFormClass' => true
                ]
            )
            ->button(
                'signin',
                [
                    'label' => 'Sign in',
                    'submit' => [
                        'action' => Security_LoginEmailPassword_Login_Submit::class,
                        'url' => 'ice_security_login',
                    ]
                ]
            );

        return [];
    }

    /**
     * @param Security_Account $accountLoginPasswordModelClass
     * @return $this
     */
    public function setAccountLoginPasswordModelClass($accountLoginPasswordModelClass)
    {
        $this->accountLoginPasswordModelClass = $accountLoginPasswordModelClass;
        return $this;
    }

    /**
     * @param Security_Account $accountEmailPasswordModelClass
     * @return $this
     */
    public function setAccountEmailPasswordModelClass($accountEmailPasswordModelClass)
    {
        $this->accountEmailPasswordModelClass = $accountEmailPasswordModelClass;
        return $this;
    }

    /**
     * Verify account by form values
     *
     * @param Security_Account|Model $account
     * @param array $values
     * @return boolean
     */
    public function verify(Security_Account $account, $values)
    {
        return false;
    }
}