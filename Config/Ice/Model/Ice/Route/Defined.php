<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 16.02.13
 * Time: 18:24
 * To change this template use File | Settings | File Templates.
 */

return array(
    100010 => array(
        'route' => '/',
        'actions' => 'Main',
        'titleAction' => 'Главная'
    ),
    100020 => array(
        'route' => '/registration/',
        'actions' => 'Account_Registration',
        'layoutAction' => 'Layout_Account',
        'params' => array(
            'accountType' => 'Login_Password',
        ),
        'titleAction' => 'Регистрация'
    ),
    100030 => array(
        'route' => '/authorization/',
        'actions' => 'Account_Authorization',
        'layoutAction' => 'Layout_Account',
        'params' => array(
            'accountType' => 'Login_Password',
        ),
        'titleAction' => 'Авторизация'
    ),
    100040 => array(
        'route' => '/logout/',
        'actions' => 'Account_Logout',
        'params' => array(
            'redirect' => '/'
        ),
        'titleAction' => 'Выход'
    ),
);