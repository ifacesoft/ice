<?php

namespace Ice\Form\Security\Login;

use Ice\Core\Form_Security_Login;
use Ice\Core\Logger;
use Ice\Model\Account;
use Ice\Model\User_Role_Link;

class Login_Password extends Form_Security_Login
{
    function __construct($key)
    {
        parent::__construct($key);

        $resource = Login_Password::getResource();

        $this->text('login', $resource->get('login'), $resource->get('login_placeholder'), ['Ice:Length_Min' => 2, 'Ice:LettersNumbers'])
            ->password('password', $resource->get('password'), $resource->get('password_placeholder'), ['Ice:Length_Min' => 5]);
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
        if ($error = $this->validate()) {
            Form_Security_Login::getLogger()->fatal($error, __FILE__, __LINE__);
        }

        foreach (Account::queryBy('login', $this->getValues()['login'], ['password', 'user__fk'])->getRows() as $accountRow) {
            if (password_verify($this->getValues()['password'], $accountRow['password'])) {
                $_SESSION['userPk'] = $accountRow['user__fk'];
                $_SESSION['roleKeys'] = User_Role_Link::queryBy('user__fk', $accountRow['user__fk'], 'role__fk')->getColumn();
                return;
            }
        }

        Form_Security_Login::getLogger()->fatal('Authorization failed: login-password incorrect', __FILE__, __LINE__);
    }
}