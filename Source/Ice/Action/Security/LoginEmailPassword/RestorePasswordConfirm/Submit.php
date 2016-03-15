<?php

namespace Ice\Action;

use Ice\Widget\Security_EmailPassword_RestorePasswordConfirm;
use Ice\Widget\Security_LoginEmailPassword_RestorePasswordConfirm;
use Ice\Widget\Security_LoginPassword_RestorePasswordConfirm;

class Security_LoginEmailPassword_RestorePasswordConfirm_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_LoginEmailPassword_RestorePasswordConfirm $form */
        $form = $input['widget'];

        $output = Security_LoginPassword_RestorePasswordConfirm_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => Security_LoginPassword_RestorePasswordConfirm::getInstance($form->getInstanceKey())
                ->setAccountModelClass($form->getAccountLoginPasswordModelClass())
                ->bind(['login' => $form->getValue('username')])
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return Security_EmailPassword_RestorePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => Security_EmailPassword_RestorePasswordConfirm::getInstance($form->getInstanceKey())
                ->setAccountModelClass($form->getAccountEmailPasswordModelClass())
                ->bind(['email' => $form->getValue('username')])
        ]);
    }
}