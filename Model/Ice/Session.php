<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dp
 * Date: 22.02.13
 * Time: 10:17
 * To change this template use File | Settings | File Templates.
 */

namespace ice\model\ice;

use ice\core\Data_Provider;
use ice\core\helper\Date;
use ice\core\helper\Request;
use ice\core\Model;

class Session extends Model
{
    /**
     * @desc Текущая сессия
     *
     * var Session
     */
    private static $_session = null;

    /**
     * Получаем текущую сессию
     *
     * @return Session|null
     */
    public static function getCurrent()
    {
        if (self::$_session) {
            return self::$_session;
        }

        $sessionPk = Data_Provider::getInstance('Session:php/')->get('PHPSESSID');

        self::$_session = Session::getModel($sessionPk, array('/pk', 'user__fk'));

        if (self::$_session) {
            self::$_session->update('last_active', Date::getCurrent());

            return self::$_session;
        }

        $sessionData = array(
            'session_pk' => $sessionPk,
            'last_active' => Date::getCurrent(),
            'ip' => Request::ip(),
            'user_agent' => Request::agent(),
            'auth_date' => Date::getCurrent(),
            'user__fk' => User::getGuest()->getPk()
        );

        return self::$_session = Session::create($sessionData)->insert();
    }

//    /**
//     * @param Account $account
//     */
//    public static function switchAccount($account)
//    {
//        $user = $account->getUser();
//
//        if ($user) {
//            Session::getCurrent()->update(
//                array(
//                    'account__fk' => $account->getPk(),
//                    'last_active' => Helper_Date::toUnix(),
//                    'auth_date' => Helper_Date::toUnix(),
//                    '_use_pk' => $user->getPk()
//                )
//            );
//        }
//    }

    /**
     * Очистить сессию
     */
    public static function clearSession()
    {
        $session = self::getCurrent();
        $session->delete();
        self::$_session = null;
    }

    /**
     * Установка юзера в сессию
     *
     * @param User $user
     */
    public function switchUser(User $user)
    {
        $update = array(
            'user__fk' => $user->getPk(),
            'auth_date' => Date::getCurrent(),
        );

        $this->update($update);
    }

    /**
     * установка города в сессию
     *
     * @param City $city
     */
    public function switchCity(City $city)
    {
        $this->update('city__fk', $city->getPk());

        $city->update('view_count', $city->view_count + 1);
    }
}