<?php

namespace Ice\Core;

use Ice\Helper\Arrays;
use Ice\Model\Account;
use Ice\Model\User;

abstract class Widget_Form_Security_Register extends Widget_Form
{
    /**
     * Register
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function submit()
    {
        $accountRow = Arrays::convert(
            $this->validate(),
            [
                'password' => function ($password) {
                    return password_hash($password, PASSWORD_DEFAULT);
                }
            ]
        );

        $accountRow['user'] = User::create()->save();

        Account::create($accountRow)->save();
    }
}
