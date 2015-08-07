<?php

namespace Ice\Widget\Form\Security;

use Ice\Core\Config;
use Ice\Core\Security;
use Ice\Core\Widget_Form_Security;
use Ice\Model\Account_Email_Password;
use Ice\Widget\Form\Simple;

class EmailPassword_Register extends Widget_Form_Security
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'email' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data)
            ->setResource(__CLASS__)
            ->setTemplate(Simple::class)
            ->text(
                'email',
                'Email',
                [
                    'required' => true,
                    'placeholder' => 'email_placeholder',
                    'validators' => 'Ice:Email'
                ]
            )
            ->password(
                'password',
                'Password',
                [
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5]
                ]
            )
            ->password(
                'password1',
                'Password1',
                ['placeholder' => 'password1_placeholder']
            )
            ->button('submit', 'Sign up', ['onclick' => 'POST']);
    }

    /**
     * @param array $params
     * @return EmailPassword_Register
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
     * Register
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.1
     */
    public function register()
    {
        $values = $this->validate();

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        $values['user'] = $userModelClass::create()->save();
        $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);

        Account_Email_Password::create($values)->save();
    }
}
