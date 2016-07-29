<?php

namespace Ice\Widget;

use Ice\Action\Security_LoginEmailPassword_RestorePassword_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_LoginEmailPassword_RestorePassword extends Widget_Security
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;
    private $accountLoginPasswordSubmitClass = null;
    private $accountEmailPasswordSubmitClass = null;

    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
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

    protected function build(array $input)
    {
        $this
//            ->setAccountLoginPasswordModelClass(Account_Login_Password::class)
//            ->setAccountEmailPasswordModelClass(Account_Email_Password::class)
//            ->setAccountLoginPasswordActionClass(Security_LoginPassword_RestorePassword_Submit::class)
//            ->setAccountEmailPasswordActionClass(Security_EmailPassword_RestorePassword_Submit::class)
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Restore password', ['valueResource' => true])])
            ->text(
                'username',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'username' => [
                            'providers' => Request::class,
                            'validators' => ['Ice:Length_Min' => 3]
                        ]
                    ]
                ]
            )
            ->div('ice-message', ['value' => '&nbsp;', 'encode' => false, 'resource' => false])
            ->button(
                'restore_password',
                [
                    'route' => 'ice_security_restore_password_request',
                    'submit' => Security_LoginEmailPassword_RestorePassword_Submit::class
                ]
            );

        return [];
    }
}