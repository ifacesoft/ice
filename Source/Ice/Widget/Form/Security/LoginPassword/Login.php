<?php

namespace Ice\Widget\Form;

use Ice\Core\Query;
use Ice\Core\Resource;
use Ice\Core\Security;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Data\Provider\Session;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Model\Account;
use Ice\Model\Account_Login_Password;
use Ice\Model\User_Role_Link;
use Ice\Security\Ice;
use Ice\View\Render\Php;

class Security_LoginPassword_Login extends Widget_Form_Security_Login
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'login' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data)
            ->setResource(__CLASS__)
            ->setTemplate(Simple::class)
            ->text(
                'login',
                'Login',
                [
                    'required' => true,
                    'placeholder' => 'login_placeholder',
                    'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
                ]
            )
            ->password(
                'password',
                'Password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->button('submit', 'Sign in', ['onclick' => 'POST']);
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.1
     */
    public function login()
    {
        $values = $this->validate();

        $userKey = Account_Login_Password::getSelectQuery(
            'user__fk',
            ['login' => $values['login'], 'password' => md5($values['password'])],
            ['page' => 1, 'limit' => 1]
        )->getValue('user__fk');

        if ($userKey) {
            return Security::getInstance()->login($userKey);
        }

        Widget_Form_Security_Login::getLogger()
            ->exception(
                ['Authorization failed: login-password incorrect', [], $this->getResource()],
                __FILE__,
                __LINE__
            );
    }
}
