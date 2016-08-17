<?php
namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Helper\Logger as Helper_Logger;
use Ice\Model\Log_Security;
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
        $logger = $this->getLogger();

        /** @var Security_LoginEmailPassword_Login $widget */
        $widget = $input['widget'];

        $log = Log_Security::create([
            'form_class' => get_class($widget)
        ]);

        try {
            $values = $widget->validate();

            $output = Security_LoginPassword_Login_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Security_LoginPassword_Login::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                    ->setProlongate($widget->getProlongate())// todo: так же надо прокинуть остальные свойства (redirect, timeout etc.)
                    ->set([
                        'login' => $values['username'],
                        'password' => $values['password']
                    ])
            ]);

            if (!isset($output['error'])) {
                return $output;
            }

            return Security_EmailPassword_Login_Submit::call([
                'widgets' => $input['widgets'],
                'widget' => Security_EmailPassword_Login::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                    ->setProlongate($widget->getProlongate())// todo: так же надо прокинуть остальные свойства (redirect, timeout etc.)
                    ->set([
                        'email' => $values['username'],
                        'password' => $values['password']
                    ])
            ]);
        } catch (\Exception $e) {
            $log->set('error', Helper_Logger::getMessage($e));

            $logger->save($log);

            return ['error' => $widget->getLogger()->info($e->getMessage(), Logger::DANGER, true)];
        }
    }
}