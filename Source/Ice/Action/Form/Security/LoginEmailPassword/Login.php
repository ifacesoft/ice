<?php
namespace Ice\Action;

use Ebs\Widget\Security_Login_LoginEmailPassword_Login;
use Ice\Widget\Form_Security_LoginPassword_Login as Widget_Form_Security_LoginPassword_Login;
use Ice\Widget\Form_Security_EmailPassword_Login as Widget_Form_Security_EmailPassword_Login;

class Form_Security_LoginEmailPassword_Login extends Render
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'widget' => ['default' => null, 'providers' => 'request'],
                'widgets' => ['default' => [], 'providers' => ['default', 'request']],
                'form' => ['validators' => 'Ice:Not_Empty']
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_Login_LoginEmailPassword_Login $form */
        $form = $input['form'];

        try {
            return Form_Security_LoginPassword_Login::call([
                'widgets' => $input['widgets'],
                'form' => Widget_Form_Security_LoginPassword_Login::getInstance($form->getInstanceKey())
                    ->setAccountModelClass($form->getAccountLoginPasswordModelClass())
                    ->bind(['login' => $form->getValue('username')])
            ]);

        } catch (\Exception $e) {
            return Form_Security_EmailPassword_Login::call([
                'widgets' => $input['widgets'],
                'form' => Widget_Form_Security_EmailPassword_Login::getInstance($form->getInstanceKey())
                    ->setAccountModelClass($form->getAccountEmailPasswordModelClass())
                    ->bind(['email' => $form->getValue('username')])
            ]);
        }
    }
}