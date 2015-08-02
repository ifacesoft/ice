<?php

namespace Ice\Widget\Form;

use Ice\Core\Query;
use Ice\Core\Resource;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Data\Provider\Session;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Model\Account;
use Ice\Model\Account_Email_Password;
use Ice\Model\User_Role_Link;
use Ice\Security\Ice;
use Ice\View\Render\Php;

class Security_EmailPassword_Login extends Widget_Form_Security_Login
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'email' => ['providers' => 'request'],
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
                'email',
                'Email',
                [
                    'required' => true,
                    'placeholder' => 'email_placeholder',
                    'validators' => 'Ice:Email'
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

        $account = Account_Email_Password::getSelectQuery(
            ['user__fk', 'password'],
            ['login' => $this->getValue('login')],
            ['page' => 1, 'limit' => 1]
        )->getRow();

        if (isset($account['password']) && password_verify($values['password'], $account['password'])) {
            Session::getInstance()->set(Ice::SESSION_USER_KEY, $account['user__fk']);
            Session::getInstance()->set(Ice::SESSION_AUTH_FLAG, 1);

            return;
        }

        Widget_Form_Security_Login::getLogger()
            ->exception(
                ['Authorization failed: login-password incorrect', [], $this->getResource()],
                __FILE__,
                __LINE__
            );
    }
}
