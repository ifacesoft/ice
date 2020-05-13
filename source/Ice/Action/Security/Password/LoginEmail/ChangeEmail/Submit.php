<?php

namespace Ice\Action;

use Ice\Core\Exception;
use Ice\Exception\Not_Valid;
use Ice\Exception\Security_Account_NotFound;
use Ice\Model\User;
use Ice\Validator\Email;
use Ice\Widget\Account_Password_Email_ChangeEmail;
use Ice\Widget\Account_Password_Login_ChangeEmail;

class Security_Password_LoginEmail_ChangeEmail_Submit extends Security
{

    protected static function config()
    {
        $config = parent::config();

        $config['input'] = [
            'widgets' => ['providers' => 'default', 'default' => []],
            'widget' => ['providers' => 'default'],
            'user' => ['providers' => \Ice\DataProvider\Security::class]
        ];
        return $config;
    }

    /** Run action
     *
     * @param array $input
     * @return array
     * @throws Exception
     */
    public function run(array $input)
    {
        $error = '';

        $widget = $input['widget'];

        $isEmail = Email::getInstance()->validate($widget->get(), 'email', []);

        if (!$isEmail) {
            throw new Not_Valid('Email не валиден');
        }

        $user = User::createQueryBuilder()
            ->left($widget->getAccountEmailPasswordModelClass(), ['/pk' => 'email_account_pk'])
            ->left($widget->getAccountLoginPasswordModelClass(), ['/pk' => 'login_account_pk'])
            ->eq(['/pk' => $input['user']->getPkValue()])
            ->getSelectQuery(['/pk', '/email', '/name', 'roles'])
            ->getModel();

        if (!empty($user->getRaw('email_account_pk', null))) {
            $accountEmailPasswordSubmitClass = $widget->getAccountEmailPasswordSubmitClass();
            $output = $accountEmailPasswordSubmitClass::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Email_ChangeEmail::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountEmailPasswordModelClass())
                    ->set([
                        'email' => $widget->get('email'),
                    ])
            ]);


            if (!isset($output['error'])) {
                return $output;
            }

            $error .= $output['error'];
        }

        if (!empty($user->getRaw('login_account_pk', null))) {
            $accountLoginPasswordSubmitClass = $widget->getAccountLoginPasswordSubmitClass();

            $output = $accountLoginPasswordSubmitClass::call([
                'widgets' => $input['widgets'],
                'widget' => Account_Password_Login_ChangeEmail::getInstance($widget->getInstanceKey())
                    ->setAccountModelClass($widget->getAccountLoginPasswordModelClass())
                    ->set([
                        'email' => $widget->get('email'),
                    ])
            ]);

            if (!isset($output['error'])) {
                return $output;

            }

            $error .= $output['error'];
        }

        if (!empty($error)) {
            return ['error' => $error];
        } else {
            throw new Security_Account_NotFound('У пользователя нет аккаунта доступного для изменения email');
        }

    }
}