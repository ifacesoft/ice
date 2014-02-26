<?php

namespace ice\action;

use ice\core\action\Ajaxable;
use ice\core\Action;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 14.12.13
 * Time: 16:27
 */
class Account_Logout extends Action implements Ajaxable
{
    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {


        $_SESSION = array();
        Session::getCurrent()->delete();

        $redirect = Request::referer();

        if (!empty($input['redirect'])) {
            $redirect = $input['redirect'];
        }

        $redirect = Helper_Uri::validRedirect(
            $redirect ? $redirect : '/'
        );

        return array(
            'redirect' => $redirect
        );
    }
}