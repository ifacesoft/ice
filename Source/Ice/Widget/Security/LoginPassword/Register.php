<?php

namespace Ice\Widget;

use Ice\Core\Widget_Security;

class Security_LoginPassword_Register extends Widget_Security
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    protected function build(array $input)
    {
        $output = parent::build($input);

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

        return $output;
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
}
