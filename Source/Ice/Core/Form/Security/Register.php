<?php

namespace Ice\Core;

use Ice\Helper\Arrays;
use Ice\Model\Account;
use Ice\Model\User;

abstract class Form_Security_Register extends Form
{
    /**
     * Create new instance of form security register
     *
     * @param $key
     * @param null $hash
     * @return Form_Security_Register
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected static function create($key, $hash = null)
    {
        /** @var Form_Security_Register $class */
        $class = self::getClass();

        if ($class == __CLASS__) {
            $class = 'Ice\Form\Security\Register\\' . $key;
        }

        return new $class($key);
    }

    /**
     * Register
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function submit()
    {
        if ($error = $this->validate()) {
            Form::getLogger()->fatal($error, __FILE__, __LINE__);
        }

        Logger::debugDie($error, $this);

        $accountRow = Arrays::convert(
            $this->getFields(),
            [
                'password' => function ($password) {
                    return password_hash($password, PASSWORD_DEFAULT);
                }
            ]
        );

        User::create([
            'account' => Account::create($accountRow)->insert()
        ])->insert();
    }
}