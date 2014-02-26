<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ziht
 * Date: 02.09.13
 * Time: 19:54
 * To change this template use File | Settings | File Templates.
 */

namespace ice\model\ice;

use ice\core\model\Factory_Delegate;

class Account_Type_Email_Password extends Account_Type implements Factory_Delegate
{
    private $cryptMethod = 'Crypt_Md5';

    /**
     * Регистрация через Email
     * @param array $data
     * @throws Account_Type_Exception
     * @return mixed|void
     */
    public function register(array $data)
    {
        $account = Model_Manager::byOptions(
            'Account',
            array(
                'name' => 'Email',
                'value' => $data['email']
            )
        );
        if ($account) {
            throw new Account_Type_Exception('Пользователь с таким e-mail уже зарегистрирован.');
        }
        $user = User::getCurrent();
        if ($user->isGuest()) {
            $fields = array(
                'is_active' => 1
            );

            $user = User::create($fields)->insert();
        }
        $password = Helper_Password::generatePassword();
        $crypt = new $this->cryptMethod;
        $encodePassword = $crypt->encode($password);

        $fields = array(
            'email' => $data['email'],
            'password' => $encodePassword,
            'reg_date' => Helper_Date::toUnix(),
            'account_type__fk' => $this->key(),
            'user__fk' => $user->key()
        );

        $account = Account::create($fields)->insert();

        $mailMessage = Mail_Message::create()->prepare(
            'send_password',
            $data['email'],
            $data['login'],
            $data,
            $user->key()
        );
//        допилить отправку сообщений
        $mailMessage->send();
    }

    /**
     * Авторизация через Email
     *
     * @param array $data
     * @throws Account_Type_Exception
     * @return mixed|void
     */
    public function login(array $data)
    {
        $account = Model_Manager::byOptions(
            'Account',
            array(
                'name' => 'Email',
                'value' => $data['email']
            )
        );
        if (!$account) {
            throw new Account_Type_Exception('Такой пользователь не зарегистрирован.');
        }
        $crypt = $this->cryptMethod;
        $encodePassword = $crypt->encode($data['password']);
        if ($account->password != $encodePassword) {
            throw new Account_Type_Exception('Введён неверный пароль. Попробуйте ещё раз.');
        }
        $user = User::getModel($account->user__fk);
        if (!$user) {
            throw new Account_Type_Exception('Такого пользователя не существует. Зарегистрируйтесь.');
        }
        User::setCurrent($user); //это authorize();
    }
}