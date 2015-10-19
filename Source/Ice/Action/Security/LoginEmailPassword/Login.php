<?php
namespace Ice\Action;

use Ebs\Widget\Security_LoginEmailPassword_Login;
use Ice\Widget\Security_LoginPassword_Login as Widget_Form_Security_LoginPassword_Login;
use Ice\Widget\Security_EmailPassword_Login as Widget_Form_Security_EmailPassword_Login;

class Security_LoginEmailPassword_Login extends Widget_Event
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
        /** @var Security_LoginEmailPassword_Login $form */
        $form = $input['widget'];

        try {
            return Security_LoginPassword_Login::call([
                'widgets' => $input['widgets'],
                'form' => Widget_Form_Security_LoginPassword_Login::getInstance($form->getInstanceKey())
                    ->setAccountModelClass($form->getAccountLoginPasswordModelClass())
                    ->bind(['login' => $form->getValue('username')])
            ]);

        } catch (\Exception $e) {
            return Security_EmailPassword_Login::call([
                'widgets' => $input['widgets'],
                'form' => Widget_Form_Security_EmailPassword_Login::getInstance($form->getInstanceKey())
                    ->setAccountModelClass($form->getAccountEmailPasswordModelClass())
                    ->bind(['email' => $form->getValue('username')])
            ]);
        }
    }
}