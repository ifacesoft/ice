<?php

namespace Ice\Action;

use Ebs\Model\Account_Email_Password;
use Ebs\Model\Account_Login_Password;
use Ebs\Model\Group;
use Ebs\Model\Subscriber;
use Ebs\Model\User_Data;
use Ice\Core\Debuger;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Security_Account_EmailNotConfirmed;
use Ice\Exception\Security_Account_NotFound;
use Ice\Model\User;
use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Model_Account;
use Ice\Core\Request;
use Ice\Exception\Security_Account_Register;
use Ice\Helper\Date;
use Ice\Helper\Logger;
use Ice\Helper\Type_String;
use Ice\Model\Log_Security;
use Ice\Model\Token;
use Ice\Validator\Email;
use Ice\Widget\Account_Form;
use Ebs\Action\Private_Subscriber_RegistrationRequest_New_Confirm;

abstract class Security extends Widget_Form_Event
{
    /**
     * Sing up by account
     *
     * @param Account_Form $accountForm
     * @param array $container
     * @return Model_Account
     * @throws Exception
     * @throws Error
     * @throws FileNotFound
     */
    final protected function signUp(Account_Form $accountForm, array $container = [])
    {
        $logger = $this->getLogger();

        /** @var Model_Account $accountModelClass */
        $accountModelClass = $accountForm->getAccountModelClass();

        $logSecurity = Log_Security::create([
            'form_class' => get_class($accountForm),
            'account_class' => $accountModelClass,
        ]);

        try {
            /** @var Model_Account $account */
            $account = $accountForm->getAccount();

            if ($account) {
                $logSecurity->set('account_key', $account->getPkValue());

                $account->registerVerify($accountForm->validate());

                if (!$accountForm->isSuccessOnExists()) {
                    throw new Security_Account_Register('Account already exists');
                }
            } else {
                $account = $accountModelClass::create();

                $account->registerVerify($accountForm->validate());

                $account = $account->signUp($accountForm, $container);
                
                if ($accountForm->get('mobile', 0)) {
                    User_Data::create([
                        'user__fk' => $account->get('user__fk'),
                        'greeting_date' => Date::get()
                    ])->save(true);
                }
            }

            if (!$account) {
                throw new Security_Account_Register('Account not found');
            }

            $logSecurity->set('account_key', $account->getPkValue());

            if ($accountForm->isAutologin()) {
                $account->signIn($accountForm);
            }

            $logger->save($logSecurity);
        } catch (\Exception $e) {
            $logSecurity->set('error', Logger::getMessage($e));

            $logger->save($logSecurity);

            throw $e;
        }

        return $account;
    }

    /**
     * @param Account_Form $accountForm
     * @return Model_Account
     * @throws \Exception
     */
    final protected function signIn(Account_Form $accountForm)
    {
        $logger = $this->getLogger();

        $logSecurity = Log_Security::create([
            'form_class' => get_class($accountForm),
            'account_class' => $accountForm->getAccountModelClass(),
        ]);

        try {
            $account = $accountForm->getAccount();

            if (!$account) {
//                file_put_contents(\getLogDir() . '.account.debug.log',print_r([get_class($accountForm) => $accountForm->get()], true) . "\n\n\n", FILE_APPEND);

                throw new Security_Account_NotFound('Account not found');
            }

            $logSecurity->set(['account_key' => $account->getPkValue()]);

            $account->loginVerify($accountForm->validate());

            $account = $account->signIn($accountForm, 'Ice\DataSource\Mysqli/front.ebs');

            $logger->save($logSecurity);

            return $account;
        } catch (Security_Account_EmailNotConfirmed $e) {
            throw $e;
        } catch (\Exception $e) {
            $logSecurity->set('error', Logger::getMessage($e));

            $logger->save($logSecurity);

            throw $e;
        }
    }

    /**
     * @param Model_Account $account
     * @return Model_Account
     * @throws Exception
     * @throws \Exception
     */
    final protected function signOut(Model_Account $account)
    {
        $logger = $this->getLogger();

        $logSecurity = Log_Security::create([
            'form_class' => '',
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue(),
        ]);

        try {
            $account = $account->signOut();

            $logger->save($logSecurity);

            return $account;
        } catch (\Exception $e) {
            $logSecurity->set('error', Logger::getMessage($e));

            $logger->save($logSecurity);

            throw $e;
        }
    }

    /**
     * @param Token $token
     * @param array $input
     * @return Model_Account
     * @throws Exception
     */
    final protected function registerConfirm(Token $token, array $input)
    {
        $logger = $this->getLogger();

        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        /** @var Model_Account $accountClass */
        $accountClass = $token->get('modelClass');

        $logSecurity = Log_Security::create([
            'form_class' => get_class($accountForm),
            'account_class' => $accountClass,
        ]);

        /** @var Model_Account $account */
        $account = $accountClass::getSelectQuery('*', ['token' => $token])->getModel();

        if (!$account) {
            $error = 'Account not found';

            $logSecurity->set('error', $logger->info($error, Core_Logger::DANGER, true));

            $logger->save($logSecurity);

            return $accountForm->getLogger()->exception([$error, [], $accountForm->getResource()], __FILE__, __LINE__);
        }

        $logSecurity->set('account_key', $account->getPkValue());

        $logger->save($logSecurity);

        $tokenData = $token->get('/data');

        $tokenData['used_class'] = get_class($account);
        $tokenData['used_id'] = $account->getPkValue();

        /** @var User $user */
        $user = $account->fetchOne(User::class, '*', true);

        $token->set([
            '/used_at' => Date::get(),
            '/data' => $tokenData
        ]);

        $account->set([
            '/expired' => isset($tokenData['account_expired']) ? $tokenData['account_expired'] : $user->get('/expired_at'),
            'token__fk' => null,
            '/confirm_at' => Date::get(),
            'confirm_data' => ['ip' => Request::ip(), 'session' => session_id()],
            'email_confirmed' => 1
        ]);

        $user->set([
            '/expired_at' => isset($tokenData['account_expired']) ? $tokenData['account_expired'] : $user->get('/expired_at'),
            '/active' => 1
        ]);

        if ($tokenData['function'] == 'registerConfirm') {
            $subscriber = Subscriber::getSelectQuery(['domain', '/pk', 'auto_confirmation'], ['/pk' => $user->get('previous_subscriber_id')])->getModel();
            //
            $writeUserCallback = function ($user) {
                Private_Subscriber_RegistrationRequest_New_Confirm::call(
                    [
                        'user_pk' => $user->getPkValue(),
                        'subscriber_pk' => $user->get('previous_subscriber_id')
                    ],
                    0,
                    true
                );
            };
            //если авторегистрация включена
            if ($subscriber->get('auto_confirmation', 0)) {
                $writeUserCallback($user);
                //если есть домены для автоподверждения
            } else {
                if ($subscriberDomains = $subscriber->get('domain')) {
                    foreach (explode(',', $subscriberDomains) as $subscriberDomain) {
                        $subscriberDomain = mb_strtolower(trim($subscriberDomain));
                        if (!empty($subscriberDomain) && (Type_String::endsWith(mb_strtolower($user->getEmail()), $subscriberDomain) || $subscriberDomain === '*')) {
                            $writeUserCallback($user);
                            break;
                        }
                    }
                }
            }
        }

        if ($tokenData['function'] == 'createChangeMailToken') {
            if (empty($tokenData['email_to_change'])) {
                throw new Error('Пустой электронный адрес для изменения');
            }

            //эбс модель
            if ($account instanceof Account_Email_Password) {
                $account->set(['email' => $tokenData['email_to_change']]);

                $accountLogin = Account_Login_Password::createQueryBuilder()
                    ->eq(['user' => $user])
                    ->getSelectQuery('/pk')
                    ->getModel();

                if (!$accountLogin) {
                    $user->set('/login', $tokenData['email_to_change']);
                }

            }

            $user->set(['/email' => $tokenData['email_to_change']]);

//            if ($account instanceof Account_Login_Password && Email::getInstance()->validate(['email' => $account->get('login')], 'email', [])) {
//                $account->set(['login' => $tokenData['email_to_change']]);
//            }

//            if (Email::getInstance()->validate(['login' => $user->get('/login')], 'login', [])) {
//                $user->set('/login', $tokenData['email_to_change']);
//            }
        }

        $account->save();
        $token->save();
        $user->save();

        $logger->save($logSecurity);

        return $account;
    }

    /**
     * @param Model_Account $account
     * @param $input
     * @return null
     * @throws Exception
     */
    final protected function restorePassword(Model_Account $account, $input)
    {
        $logger = $this->getLogger();

        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $accountModelClass = get_class($account);

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($accountForm)
        ]);

        $token = Token::getModel($account->get('token__fk', false), '*');

//        if ($token) {
//            $tokenData = $token->get('token_data');
//
//            if (isset($tokenData['function']) && $tokenData['function'] != __FUNCTION__) {
//                throw new Error('Account not confirmed');
//            }
//        }

        $token = Token::create([
            '/' => md5(Type_String::getRandomString()),
            '/expired' => $accountForm->getConfirmationExpired(),
            'modelClass' => $accountModelClass,
            'token_data' => ['function' => __FUNCTION__, 'account' => [get_class($account) => $account->getPkValue()]]
        ])->save();

        $account->set(['token' => $token])->save();

        $this->sendRestorePasswordConfirm($token, $input);

        $logger->save($log);

        return $account;
    }

    /**
     * @param Model_Account $account
     * @param $input
     * @return null
     * @throws Exception
     */
    final protected function createChangeMailToken(Model_Account $account, $input)
    {
        $logger = $this->getLogger();

        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $accountModelClass = get_class($account);

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($accountForm)
        ]);

        $token = Token::getModel($account->get('token__fk', false), '*');

//        if ($token) {
//            $tokenData = $token->get('token_data');
//
//            if (isset($tokenData['function']) && $tokenData['function'] != __FUNCTION__) {
//                throw new Error('Account not confirmed');
//            }
//        }

        $token = Token::create([
            '/' => md5(Type_String::getRandomString()),
            '/expired' => $accountForm->getConfirmationExpired(),
            'modelClass' => $accountModelClass,
            'token_data' => ['function' => __FUNCTION__, 'email_to_change' => $accountForm->get('email')],
        ])->save();

        $account->set(['token' => $token])->save();

        $this->sendChangeEmailConfirm($token, $input);

        $logger->save($log);

        return $account;
    }

    /**
     * Return confirm token and confirm token expired
     *
     * @param Token $token
     * @throws Exception
     */
    public function sendRestorePasswordConfirm(Token $token, array $input)
    {
        Core_Logger::getInstance(__CLASS__)
            ->exception(['Implement {$0} for {$1}', [__FUNCTION__, get_class($this)]], __FILE__, __LINE__);
    }

    /**
     * Return confirm token and confirm token expired
     *
     * @param Token $token
     * @throws Exception
     */
    public function sendChangeEmailConfirm(Token $token, array $input)
    {
        Core_Logger::getInstance(__CLASS__)
            ->exception(['Implement {$0} for {$1}', [__FUNCTION__, get_class($this)]], __FILE__, __LINE__);
    }

    /**
     * @param $account
     * @param $accountData
     * @param $input
     * @return Model_Account|null
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @throws Config_Error
     */
    final protected function changePassword($account, $accountData, $input)
    {
        $logger = $this->getLogger();

        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $log = Log_Security::create([
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($accountForm)
        ]);

        /** @var User $user */
        $user = $account->fetchOne(User::class, '/active', true);

        if (!$user || !$user->isActive()) {
            $error = 'User is blocked or not found';

            $log->set('error', $logger->info($error, Core_Logger::DANGER, true));

            $logger->save($log);

            return $accountForm->getLogger()->exception([$error, [], $accountForm->getResource()], __FILE__, __LINE__);
        }

        /** @var Model_Account $account */
        $account = $account->set($accountData)->save();

        $logger->save($log);

        return $account;
    }
}