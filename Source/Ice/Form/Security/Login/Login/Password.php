<?php

namespace Ice\Form\Security\Login;

use Ice\Core\Form_Security_Login;

class Login_Password extends Form_Security_Login
{
    function __construct($key)
    {
        parent::__construct($key);

        $resource = Login_Password::getResource();

        $this->text('login', $resource->get('login'), $resource->get('login_placeholder'), ['Ice:Length_Min' => 2, 'Ice:LettersNumbers'])
            ->password('password', $resource->get('password'), $resource->get('password_placeholder'), ['Ice:Length_Min' => 5]);
    }
}