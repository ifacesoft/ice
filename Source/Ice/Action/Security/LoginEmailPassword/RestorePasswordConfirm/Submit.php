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

        $accountLoginPasswordSubmitClass = $form->getAccountLoginPasswordSubmitClass();

        $output = $accountLoginPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        $accountEmailPasswordSubmitClass = $form->getAccountEmailPasswordSubmitClass();

        return $accountEmailPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);
    }
}