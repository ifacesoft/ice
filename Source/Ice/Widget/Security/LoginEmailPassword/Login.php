<?php
namespace Ice\Widget;

use Ice\Action\Security_LoginEmailPassword_Login_Submit;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_LoginEmailPassword_Login extends Widget_Security
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;

    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [
                'username' => ['providers' => Request::class],
                'password' => ['providers' => Request::class]
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
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

    protected function build(array $input)
    {
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Login')])
            ->text(
                'username',
                [
                    'required' => true,
                    'placeholder' => 'username_placeholder',
                    'validators' => ['Ice:Length_Min' => 3],
                ]
            )
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                ]
            )
            ->div('ice-message', ['label' => '&nbsp;', 'resource' => false])
            ->button(
                'login',
                [
                    'submit' => [
                        'action' => Security_LoginEmailPassword_Login_Submit::class,
                        'url' => 'ice_security_login_request',
                    ]
                ]
            );

        return [];
    }
}