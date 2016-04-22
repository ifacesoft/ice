<?php
namespace Ice\Widget;

use Ice\Action\Security_LoginEmailPassword_ChangePassword_Submit;
use Ice\Core\Model;
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
            'input' => [
                'password' => ['providers' => Request::class],
                'new_password' => ['providers' => Request::class],
                'confirm_password' => ['providers' => Request::class]
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null],
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
            ->widget('header', ['widget' => $this->getWidget(Header::class)->h1('Change password')])
            ->password(
                'password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                ]
            )
            ->password(
                'new_password',
                [
                    'required' => true,
                    'placeholder' => 'new_password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => 'confirm_password_placeholder',
                    'required' => true
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