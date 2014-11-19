<?php
/**
 * Security provider login-password implementation class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Security\Provider;

use Ice\Core\Form;
use Ice\Core\Security_Provider;
use Ice\Form\Simple;
use Ice\Helper\Defaults;

/**
 * Class Login_Password
 *
 * Login password security provider
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.1
 * @since 0.1
 */
class Login_Password extends Security_Provider
{

    public function getRegisterForm(array $data = array())
    {
        $data = Defaults::get($data, ['login' => '', 'password' => '', 'password1' => '']);

        $resource = Login_Password::getResource();

        return Simple::getInstance('Register')
            ->text('login', $resource->get('login'), $resource->get('login_placeholder'), ['Ice:Not_Empty', 'Ice:Length_Min' => 2, 'Ice:LettersNumbers'], $data['login'])
            ->password('password', $resource->get('password'), $resource->get('password_placeholder'), ['Ice:Not_Empty', 'Ice:Length_Min' => 5], $data['password'])
            ->password('password1', $resource->get('password1'), $resource->get('password1_placeholder'), ['Ice:Not_Empty', 'Ice:Length_Min' => 5, 'Ice:Equal' => $data['password']], $data['password1']);
    }

    public function getLoginForm(array $data = array())
    {
        // TODO: Implement getLoginForm() method.
    }
}