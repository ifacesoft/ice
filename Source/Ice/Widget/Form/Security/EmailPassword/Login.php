<?php

namespace Ice\Widget\Form\Security\Login;

use Ice\Core\Query;
use Ice\Core\Resource;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Model\Account;
use Ice\Model\User_Role_Link;
use Ice\View\Render\Php;

class Security_EmailPassword_Login extends Widget_Form_Security_Login
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    protected function __construct()
    {
        parent::__construct();

        $resource = Security_LoginPassword_Login::getResource();

        $this->text(
            'login',
            $resource->get('login'),
            [
                'placeholder' => $resource->get('login_placeholder'),
                'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
            ]
        )->password(
            'password',
            $resource->get('password'),
            [
                'placeholder' => $resource->get('password_placeholder'),
                'validators' => ['Ice:Length_Min' => 5]
            ]
        );
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function login()
    {
        foreach (Query::getBuilder(Account::getClass())->eq(['login' => $this->getValues()['login']])->getSelectQuery(['password', 'user__fk'])->getRows() as $accountRow) {
            if (password_verify($this->validate()['password'], $accountRow['password'])) {
                $_SESSION['userPk'] = $accountRow['user__fk'];
                $_SESSION['roleNames'] = Query::getBuilder(User_Role_Link::getClass())
                    ->inner('Ice:Role', 'role_name')
                    ->eq(['user__fk', $accountRow['user__fk']])
                    ->getSelectQuery('role_name')->getColumn();
                return;
            }
        }

        Widget_Form_Security_Login::getLogger()->exception('Authorization failed: login-password incorrect', __FILE__, __LINE__);
    }
}
