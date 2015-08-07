<?php
namespace Ice\Widget\Form\Security;

use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security;

class LoginEmailPassword_Login extends Widget_Form_Security
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;

    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'username' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data)
            ->setResource(__CLASS__)
            ->text(
                'username',
                'Username',
                [
                    'required' => true,
                    'placeholder' => 'username_placeholder',
                    'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers'],
                    'srOnly' => true,
                    'resetFormClass' => true
                ]
            )
            ->password(
                'password',
                'Password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                    'srOnly' => true,
                    'resetFormClass' => true
                ]
            )
            ->button('submit', 'Sign in', ['classes' => 'button-blue', 'onclick' => 'POST']);
    }

    public function login()
    {
        try {
            LoginPassword_Login::create($this->getUrl(), $this->getAction())
                ->setAccountModelClass($this->accountLoginPasswordModelClass)
                ->bind(['login' => $this->getValue('username')])
                ->login();
        } catch (\Exception $e) {
            EmailPassword_Login::create($this->getUrl(), $this->getAction())
                ->setAccountModelClass($this->accountEmailPasswordModelClass)
                ->bind(['email' => $this->getValue('username')])
                ->login();
        }
    }

    /**
     * @param Security_Account $accountLoginPasswordModelClass
     * @return LoginEmailPassword_Login
     */
    public function setAccountLoginPasswordModelClass($accountLoginPasswordModelClass)
    {
        $this->accountLoginPasswordModelClass = $accountLoginPasswordModelClass;
        return $this;
    }

    /**
     * @param Security_Account $accountEmailPasswordModelClass
     * @return LoginEmailPassword_Login
     */
    public function setAccountEmailPasswordModelClass($accountEmailPasswordModelClass)
    {
        $this->accountEmailPasswordModelClass = $accountEmailPasswordModelClass;
        return $this;
    }
}