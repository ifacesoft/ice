<?php

namespace Ice\Form\Security\Register;

use Ice\Core\Form_Security_Register;

class Login_Password extends Ui_Form_Security_Register
{
    function __construct($key)
    {
        parent::__construct($key);

        $resource = Login_Password::getResource();

        $this->text(
            'login',
            $resource->get('login'),
            ['placeholder' => $resource->get('login_placeholder')],
            ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
        )->password(
            'password',
            $resource->get('password'),
            ['placeholder' => $resource->get('password_placeholder')],
            ['Ice:Length_Min' => 5]
        )->password(
            'password1',
            $resource->get('password1'),
            ['placeholder' => $resource->get('password1_placeholder')]
        );
    }

    public function bind($key, $value = null)
    {
        if ($key == 'password1') [
            $this->_validateScheme['password1']['Ice:Equal'] = $this->_values['password']
        ];

        return parent::bind($key, $value);
    }
}