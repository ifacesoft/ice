<?php
namespace Ice\Widget;

use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Widget\Form_Security_LoginPassword_Login;

class Form_Security_LoginEmailPassword_Login extends Widget_Form_Security_Login
{
    private $accountLoginPasswordModelClass = null;
    private $accountEmailPasswordModelClass = null;

    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null],
            'input' => [
                'username' => ['providers' => 'request'],
                'password' => ['providers' => 'request']
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    protected function build(array $input)
    {
        parent::build($input);

        $this
            ->text(
                'username',
                [
                    'label' => 'Username',
                    'required' => true,
                    'placeholder' => 'username_placeholder',
                    'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers'],
                    'srOnly' => true,
                    'resetFormClass' => true
                ]
            )
            ->password(
                'password',
                [
                    'label' => 'Password',
                    'required' => true,
                    'placeholder' => 'password_placeholder',
                    'validators' => ['Ice:Length_Min' => 5],
                    'srOnly' => true,
                    'resetFormClass' => true
                ]
            )
            ->submit('signin', ['label' => 'Sign in']);
    }

    public function action($token)
    {
        try {
            return $this->getWidget(Form_Security_LoginPassword_Login::getClass(), $this->getInstanceKey())
                ->setAccountModelClass($this->accountLoginPasswordModelClass)
                ->bind(['login' => $this->getValue('username')])
                ->action($token);
        } catch (\Exception $e) {
            return $this->getWidget(Form_Security_EmailPassword_Login::getClass(), $this->getInstanceKey())
                ->setAccountModelClass($this->accountEmailPasswordModelClass)
                ->bind(['email' => $this->getValue('username')])
                ->action($token);
        }
    }

    /**
     * @param Security_Account $accountLoginPasswordModelClass
     * @return $this
     */
    public function setAccountLoginPasswordModelClass($accountLoginPasswordModelClass)
    {
        $this->accountLoginPasswordModelClass = $accountLoginPasswordModelClass;
        return $this;
    }

    /**
     * @param Security_Account $accountEmailPasswordModelClass
     * @return $this
     */
    public function setAccountEmailPasswordModelClass($accountEmailPasswordModelClass)
    {
        $this->accountEmailPasswordModelClass = $accountEmailPasswordModelClass;
        return $this;
    }

    /**
     * Verify account by form values
     *
     * @param Security_Account|Model $account
     * @param array $values
     * @return boolean
     */
    protected function verify(Security_Account $account, $values)
    {
        return false;
    }
}