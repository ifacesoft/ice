<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_LoginEmail_RestorePasswordConfirm_Submit;
use Ice\DataProvider\Request;

class Account_Password_LoginEmail_RestorePasswordConfirm extends Account_Form
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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
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
//            ->setAccountLoginPasswordActionClass(Security_Password_Login_RestorePassword_Submit::class)
//            ->setAccountEmailPasswordActionClass(Security_Password_Email_RestorePassword_Submit::class)
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password confirmation', ['valueResource' => true])])
            ->text(
                'token',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => ['token' => ['providers' => Request::class]]
                ]
            )
            ->password(
                'new_password',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => ['new_password' => ['providers' => Request::class]]
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => [
                        'confirm_password' => [
                            'providers' => Request::class,
                            'validators' => [
                                'Ice:Equal' => [
                                    'value' => $this->get('new_password'),
                                    'message' => 'Passwords must be equals'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'restore_password_confirm',
                [
                    'route' => 'ice_security_restore_password_confirm_request',
                    'submit' => Security_Password_LoginEmail_RestorePasswordConfirm_Submit::class
                ]
            );
    }
}