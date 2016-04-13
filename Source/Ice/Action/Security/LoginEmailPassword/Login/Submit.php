<?php
namespace Ice\Action;

use Ice\Core\Debuger;
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

        $form->bind(['login' => $form->getValue('username')]);
        
        $output = Security_LoginPassword_Login_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        $form->bind(['email' => $form->getValue('username')]);
        
        return Security_EmailPassword_Login_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);
    }
}