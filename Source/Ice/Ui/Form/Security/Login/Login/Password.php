<?php

namespace Ice\Form\Security\Login;

use Ice\Core\Form_Security_Login;
use Ice\Core\Query;
use Ice\Model\Account;
use Ice\Model\User_Role_Link;

class Login_Password extends Ui_Form_Security_Login
{
    function __construct($key)
    {
        parent::__construct($key);

        $resource = Login_Password::getResource();

        $this->text(
            'login',
            $resource->get('login'),
            ['placeholder' => $resource->get('login_placeholder')],
            ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
        )->password(
            'password',
            $resource->get('password'),
            ['placeholder' => $resource->get('password_placeholder')],
            ['Ice:Length_Min' => 5]
        );
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function submit()
    {
        foreach (Query::getBuilder(Account::getClass())->eq(['login' => $this->getValues()['login']])->select(['password', 'user__fk'])->getRows() as $accountRow) {
            if (password_verify($this->validate()['password'], $accountRow['password'])) {
                $_SESSION['userPk'] = $accountRow['user__fk'];
                $_SESSION['roleNames'] = Query::getBuilder(User_Role_Link::getClass())
                    ->inner('Ice:Role', 'role_name')
                    ->eq(['user__fk', $accountRow['user__fk']])
                    ->select('role_name')->getColumn();
                return;
            }
        }

        Ui_Form_Security_Login::getLogger()->exception('Authorization failed: login-password incorrect', __FILE__, __LINE__);
    }
}