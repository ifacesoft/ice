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

        $form->bind(['login' => $form->getValue('username')]);

        $accountLoginPasswordSubmitClass = $form->getAccountLoginPasswordSubmitClass();

        $output = $accountLoginPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        $form->bind(['email' => $form->getValue('username')]);

        $accountEmailPasswordSubmitClass = $form->getAccountEmailPasswordSubmitClass();

        return $accountEmailPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);
    }
}