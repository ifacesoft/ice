<?php

namespace Ice\Widget;

use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security_Register;

class Form_Security_LoginPassword_Register extends Widget_Form_Security_Register
{
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public function init(array $input)
    {
        parent::init($input);

        $this->text(
            'login',
            [
                'placeholder' => 'login_placeholder',
                'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
            ]
        )->password(
            'password',
            [
                'placeholder' => 'password_placeholder',
                'validators' => ['Ice:Length_Min' => 5]
            ]
        )->password(
            'password1',
            ['placeholder' => 'password1_placeholder']
        );
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'password1') {
                [
                    $this->validateScheme['password1']['Ice:Equal'] = $this->getValue('password')
                ];
            }

            parent::bind([$key => $value]);
        }

        return $this;
    }


    /**
     * Register by input form data
     *
     * @param array $userData User defaults
     * @return Model|Security_Account
     */
    public function register(array $userData = [])
    {
        // TODO: Implement register() method.
    }
}
