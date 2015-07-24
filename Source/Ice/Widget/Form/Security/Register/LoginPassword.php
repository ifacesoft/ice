<?php

namespace Ice\Widget\Form\Security\Register;


use Ice\Core\Widget_Form_Security_Register;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\View\Render\Php;

class LoginPassword extends Widget_Form_Security_Register
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    protected function __construct()
    {
        parent::__construct();

        $resource = LoginPassword::getResource();

        $this->text(
            'login',
            $resource->get('login'),
            [
                'placeholder' => $resource->get('login_placeholder'),
                'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
            ]
        )->password(
            'password',
            $resource->get('password'),
            [
                'placeholder' => $resource->get('password_placeholder'),
                'validators' => ['Ice:Length_Min' => 5]
            ]
        )->password(
            'password1',
            $resource->get('password1'),
            ['placeholder' => $resource->get('password1_placeholder')]
        );
    }

    /**
     * @param array $params
     * @return LoginPassword
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
