<?php
namespace Ice\Widget;

use Ice\Action\Security_LoginEmailPassword_ChangePassword_Submit;
use Ice\Core\Widget_Security;
use Ice\DataProvider\Request;

class Security_LoginEmailPassword_ChangePassword extends Widget_Security
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;

    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [],
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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Change password', ['valueResource' => true])])
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'password' => [
                            'providers' => Request::class,
                            'validators' => ['Ice:Length_Min' => 5]
                        ]
                    ]
                ]
            )
            ->password(
                'new_password',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'new_password' => [
                            'providers' => Request::class,
                            'validators' => ['Ice:Length_Min' => 5]
                        ]
                    ]
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
            ->div('ice-message', ['value' => '&nbsp;', 'encode' => false, 'resource' => false])
            ->button(
                'change_password',
                [
                    'route' => 'ice_security_change_password_request',
                    'submit' => Security_LoginEmailPassword_ChangePassword_Submit::class
                ]
            );

        return [];
    }
}