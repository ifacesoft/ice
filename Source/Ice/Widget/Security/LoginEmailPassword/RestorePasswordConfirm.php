<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_RestoreConfirm_Submit;
use Ice\Action\Security_LoginEmailPassword_RestorePasswordConfirm_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;

class Security_LoginEmailPassword_RestorePasswordConfirm extends Widget_Security
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;
    private $accountLoginPasswordSubmitClass = null;
    private $accountEmailPasswordSubmitClass = null;

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['token' => ['providers' => [Request::class, 'default', Router::class]]],
            'output' => [],
        ];
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'confirm_password') {
                [
                    $this->validateScheme['confirm_password']['Ice:Equal'] = [
                        'value' => $this->getValue('new_password'),
                        'message' => 'Passwords must be equals'
                    ]
                ];
            }

            parent::bind([$key => $value]);
        }

        return $this;
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $this
//            ->setAccountLoginPasswordModelClass(Account_Login_Password::class)
//            ->setAccountEmailPasswordModelClass(Account_Email_Password::class)
//            ->setAccountLoginPasswordActionClass(Security_LoginPassword_RestorePassword_Submit::class)
//            ->setAccountEmailPasswordActionClass(Security_EmailPassword_RestorePassword_Submit::class)
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password confirmation', ['valueResource' => true])])
            ->text(
                'token',
                [
                    'placeholder' => 'token_placeholder',
                    'required' => true,
                ]
            )
            ->password(
                'new_password',
                [
                    'placeholder' => 'new_password_placeholder',
                    'required' => true,
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => 'confirm_password_placeholder',
                    'required' => true,
                ]
            )
            ->div('ice-message', ['value' => '&nbsp;', 'encode' => false, 'resource' => false])
            ->button(
                'restore_password_confirm',
                [
                    'route' => 'ice_security_restore_password_confirm_request',
                    'submit' => Security_LoginEmailPassword_RestorePasswordConfirm_Submit::class
                ]
            );
    }

    /**
     * @return null
     */
    public function getAccountLoginPasswordModelClass()
    {
        return $this->accountLoginPasswordModelClass;
    }

    /**
     * @param null $accountLoginPasswordModelClass
     * @return $this
     */
    public function setAccountLoginPasswordModelClass($accountLoginPasswordModelClass)
    {
        $this->accountLoginPasswordModelClass = $accountLoginPasswordModelClass;
        return $this;
    }

    /**
     * @return null
     */
    public function getAccountEmailPasswordModelClass()
    {
        return $this->accountEmailPasswordModelClass;
    }

    /**
     * @param null $accountEmailPasswordModelClass
     * @return $this
     */
    public function setAccountEmailPasswordModelClass($accountEmailPasswordModelClass)
    {
        $this->accountEmailPasswordModelClass = $accountEmailPasswordModelClass;
        return $this;
    }

    /**
     * @return null
     */
    public function getAccountLoginPasswordSubmitClass()
    {
        return $this->accountLoginPasswordSubmitClass;
    }

    /**
     * @param null $accountLoginPasswordSubmitClass
     * @return $this
     */
    public function setAccountLoginPasswordActionClass($accountLoginPasswordSubmitClass)
    {
        $this->accountLoginPasswordSubmitClass = $accountLoginPasswordSubmitClass;
        return $this;
    }

    /**
     * @return null
     */
    public function getAccountEmailPasswordSubmitClass()
    {
        return $this->accountEmailPasswordSubmitClass;
    }

    /**
     * @param null $accountEmailPasswordSubmitClass
     * @return $this
     */
    public function setAccountEmailPasswordActionClass($accountEmailPasswordSubmitClass)
    {
        $this->accountEmailPasswordSubmitClass = $accountEmailPasswordSubmitClass;
        return $this;
    }
}