<?php
namespace Ice\Action;

use Ice\Widget\Security_EmailPassword_Login;
use Ice\Widget\Security_LoginEmailPassword_Login;
use Ice\Widget\Security_LoginPassword_Login;

class Security_LoginEmailPassword_Login_Submit extends Security
{
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
            return Security_LoginPassword_Login_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Security_LoginPassword_Login::getInstance($form->getInstanceKey())
                    ->setAccountModelClass($form->getAccountLoginPasswordModelClass())
                    ->bind(['login' => $form->getValue('username')])
            ]);

        } catch (\Exception $e) {
            return Security_EmailPassword_Login_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Security_EmailPassword_Login::getInstance($form->getInstanceKey())
                    ->setAccountModelClass($form->getAccountEmailPasswordModelClass())
                    ->bind(['email' => $form->getValue('username')])
            ]);
        }
    }
}