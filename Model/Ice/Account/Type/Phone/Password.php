<?php

namespace ice\model\ice;

use ice\core\model\Factory_Delegate;

/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 14.04.13
 * Time: 18:26
 * To change this template use File | Settings | File Templates.
 */
class Account_Type_Phone_Password extends Account_Type implements Factory_Delegate
{
    /**
     * Регистрация пользователя
     *
     * @param array $data
     *
     * @throws Account_Type_Exception
     * @return mixed
     */
    public function register(array $data)
    {
        $data['phone'] = Helper_Phone::parseMobile($data['phone']);

        if (empty($data['password'])) {
            $data['password'] = '' . rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(
                    0,
                    9
                ) . rand(0, 9);

            if (!$this->sendPassword($data)) {
                throw new Account_Type_Exception('СМС сообщение не отправлено, повторите попытку.');
            }
        }

        $fields = array(
            'account_type__fk' => Account_Type::getDelegate($data['accountType'])->key(),
            'ip' => Request::ip(),
            'reg_date' => Helper_Date::toUnix(),
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'user__fk' => User::getNewUser($data['user_name'])->key()
        );

        return Account::create($fields)->insert();
    }

    /**
     * @param array $data
     * @throws Account_Type_Exception
     * @return bool
     */
    public function sendPassword(array $data)
    {
        $config = $this->getConfig();
        $messageProvider = Mail_Provider::getDelegate($config['message_provider']);

        if (!$messageProvider) {
            throw new Account_Type_Exception('Провайдер "' . $config['message_provider'] . '"для отправки сообщений не найден');
        }

        $message = Mail_Message::create()->prepare(
            $config['message_template'],
            $data['phone'],
            $data['user_name'],
            array(
                'password' => $data['password']
            ),
            $messageProvider->key()
        );

        return $message->send();
    }

    /**
     * Авторизация пользователя
     *
     * @param array $data
     *
     * @throws Account_Type_Exception
     * @return boolean
     */
    public function login(array $data)
    {
        $account = Account::getQuery()
            ->select(array('password', 'User/name'))
            ->innerJoin('User')
            ->eq('phone', Helper_Phone::parseMobile($data['phone']))
            ->limit(1)
            ->execute()
            ->getModel();

        if (!password_verify($data['password'], $account->password)) {
            throw new Account_Type_Exception('Пользователь не зарегистрирован.');
        }

        User::setCurrent($account->User);

        return true;
    }

}