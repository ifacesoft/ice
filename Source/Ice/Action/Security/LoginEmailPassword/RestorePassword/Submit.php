<?php

namespace Ice\Action;

use Ice\Core\Model;
use Ice\Widget\Security_EmailPassword_RestorePassword;
use Ice\Widget\Security_LoginEmailPassword_RestorePassword;
use Ice\Widget\Security_LoginPassword_RestorePassword;

class Security_LoginEmailPassword_RestorePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_LoginEmailPassword_RestorePassword $form */
        $form = $input['widget'];

        $output = Security_LoginPassword_RestorePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => Security_LoginPassword_RestorePassword::getInstance($form->getInstanceKey())
                ->setAccountModelClass($form->getAccountLoginPasswordModelClass())
                ->bind(['login' => $form->getValue('username')])
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return Security_EmailPassword_RestorePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => Security_EmailPassword_RestorePassword::getInstance($form->getInstanceKey())
                ->setAccountModelClass($form->getAccountEmailPasswordModelClass())
                ->bind(['email' => $form->getValue('username')])
        ]);
    }
}