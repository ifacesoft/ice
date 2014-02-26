<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dp
 * Date: 24.02.13
 * Time: 22:49
 * To change this template use File | Settings | File Templates.
 */

namespace ice\model\ice;

use ice\core\helper\Date;
use ice\core\helper\Request;
use ice\core\model\Factory_Delegate;

class Account_Type_Login_Password extends Account_Type implements Factory_Delegate
{
    /**
     * Регистрация пользователя
     *
     * @param array $data
     *
     * @throws Account_Type_Exception
     * @return Account
     */
    public function register(array $data)
    {
        return Account::create(
            array(
                'account_type' => Account_Type::getDelegate($data['accountType']),
                'ip' => Request::ip(),
                'reg_date' => Date::getCurrent(),
                'login' => $data['login'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'user' => User::getNewUser($data['login'])
            )
        )->insert();
    }

    /**
     * Авторизация пользователя
     *
     * @param array $data
     *
     * @throws Account_Type_Exception
     * @return User
     */
    public function login(array $data)
    {
        $account = Account::getQueryBuilder()
            ->select(array('password', 'user__fk', 'User/name'))
            ->inner(User::getClass())
            ->eq('login', $data['login'])
            ->limit(1)
            ->execute()
            ->getModel();

        if (!$account) {
            throw new Account_Type_Exception('Пользователь не зарегистрирован.');
        }

        if (!password_verify($data['password'], $account->get('password'))) {
            throw new Account_Type_Exception('Отказано в доступе. Проверьте введенный пароль.');
        }

        User::setCurrent($account->get(User::getClass()));

        return User::getCurrent();
    }
}