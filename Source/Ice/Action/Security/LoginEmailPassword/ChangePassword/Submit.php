<?php
namespace Ice\Action;

use Ice\Widget\Security_LoginEmailPassword_ChangePassword;

class Security_LoginEmailPassword_ChangePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_LoginEmailPassword_ChangePassword $form */
        $form = $input['widget'];

        $output = Security_LoginPassword_ChangePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return Security_EmailPassword_ChangePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);
    }
}