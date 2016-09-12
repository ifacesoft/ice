<?php
namespace Ice\Action;

use Ice\Widget\Account_Password_LoginEmail_ChangePassword;

class Security_Password_LoginEmail_ChangePassword_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Security_Password_LoginEmail_ChangePassword $form */
        $form = $input['widget'];

        $output = Security_Password_Login_ChangePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);

        if (!isset($output['error'])) {
            return $output;
        }

        return Security_Password_Email_ChangePassword_Submit::call([
            'widgets' => $input['widgets'],
            'widget' => $form
        ]);
    }
}