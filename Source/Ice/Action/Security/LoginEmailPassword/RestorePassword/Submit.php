<?php

namespace Ice\Action;

use Ice\Widget\Security_LoginEmailPassword_RestorePassword;

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

        $form->set(['login' => $form->getPart('username')->get('username')]);

        $accountLoginPasswordSubmitClass = $form->getAccountLoginPasswordSubmitClass();

        $output = $accountLoginPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        $form->set(['email' => $form->getPart('username')->get('username')]);

        $accountEmailPasswordSubmitClass = $form->getAccountEmailPasswordSubmitClass();

        return $accountEmailPasswordSubmitClass::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);
    }
}