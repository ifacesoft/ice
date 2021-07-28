<?php

namespace Ice\Action;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Model_Account;
use Ice\Core\Request;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Security_Account_EmailNotConfirmed;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_Register;
use Ice\Helper\Date;
use Ice\Helper\Type_String;
use Ice\Model\Log_Security;
use Ice\Model\Token;
use Ice\Model\User;
use Ice\Widget\Account_Form;

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
            'widget_class' => get_class($accountForm),
            'account_class' => $accountModelClass,
            'action_class' => get_class($this)
        ]);

        try {
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
            }

            if (!$account) {
                throw new Security_Account_Register('Account not found');
            }

            $logSecurity->set('account_key', $account->getPkValue());

            $logger->save($logSecurity);
        } catch (\Exception $e) {
            $logSecurity->set([
                'error' => $e->getMessage(),
                'exception' => \Ifacesoft\Ice\Core\Domain\Exception\Error::create(__METHOD__, 'Failed', $accountForm->get(), $e)->get()
            ]);

            $logger->save($logSecurity);

            throw $e;
        }

        if ($accountForm->isAutologin()) {
            $this->signIn($accountForm, $container);
        }

        return $account;
    }

    /**
     * @param Account_Form $accountForm
     * @return Model_Account
     * @throws \Exception
     */
    final protected function signIn(Account_Form $accountForm, array $container = [])
    {
        $logger = $this->getLogger();

        $logSecurity = Log_Security::create([
            'widget_class' => get_class($accountForm),
            'account_class' => $accountForm->getAccountModelClass(),
            'action_class' => $accountForm->get('subject', get_class($this))
        ]);

        try {
            $account = $accountForm->getAccount();

            if (!$account) {
                throw new Security_Account_NotFound('Account not found');
            }

            $logSecurity->set(['account_key' => $account->getPkValue()]);

            $account->loginVerify($accountForm->validate());

            $account = $account->signIn($accountForm);

            $logger->save($logSecurity);

            return $account;
        } catch (Security_Account_EmailNotConfirmed $e) {
            throw $e;
        } catch (\Exception $e) {
            $logSecurity->set([
                'error' => $e->getMessage(),
                'exception' => \Ifacesoft\Ice\Core\Domain\Exception\Error::create(__METHOD__, 'Failed', $accountForm->get(), $e)->get()
            ]);

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
            'widget_class' => '',
            'account_class' => get_class($account),
            'action_class' => get_class($this),
            'account_key' => $account->getPkValue(),
        ]);

        try {
            $account = $account->signOut();

            $logger->save($logSecurity);

            return $account;
        } catch (\Exception $e) {
            $logSecurity->set([
                'error' => $e->getMessage(),
                'exception' => \Ifacesoft\Ice\Core\Domain\Exception\Error::create(__METHOD__, 'Failed', [], $e)->get()
            ]);

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
            'widget_class' => get_class($accountForm),
            'account_class' => $accountClass,
            'action_class' => get_class($this)
        ]);

        /** @var Model_Account $account */
        $account = $accountClass::getSelectQuery('*', ['token' => $token])->getModel();

        if (!$account) {
            $error = 'Account not found';

            $logSecurity->set(['error' => $error]);

            $logger->save($logSecurity);

            return $accountForm->getLogger()->exception([$error, [], $accountForm->getResource()], __FILE__, __LINE__);
        }

        $logSecurity->set('account_key', $account->getPkValue());

        $logger->save($logSecurity);

        /** @var User $user */
        $user = $account->fetchOne(User::class, '*', true);

        $expiredAt = isset($tokenData['account_expired']) ? $tokenData['account_expired'] : $user->get('/expired_at');

        $account->set([
            '/expired' => $expiredAt,
            'token__fk' => null,
            '/confirm_at' => Date::get(),
            'confirm_data' => ['ip' => Request::ip(), 'session' => session_id()],
            'email_confirmed' => 1
        ])->save();

        $tokenData = $token->get('/data');

        $tokenData['used_class'] = get_class($account);
        $tokenData['used_id'] = $account->getPkValue();

        $token->set([
            '/used_at' => Date::get(),
            '/data' => $tokenData
        ])->save();

        $user->set([
            '/expired_at' => $expiredAt,
            '/active' => 1
        ])->save();

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

        $logSecurity = Log_Security::create([
            'account_class' => $accountModelClass,
            'account_key' => $account->getPkValue(),
            'widget_class' => get_class($accountForm),
            'action_class' => $accountForm->get('subject', get_class($this))
        ]);

        $token = Token::create([
            '/' => md5(Type_String::getRandomString()),
            '/expired' => $accountForm->getConfirmationExpired(),
            'modelClass' => $accountModelClass,
            'token_data' => ['function' => __FUNCTION__, 'account' => [get_class($account) => $account->getPkValue()]]
        ])->save();

        $account->set(['token' => $token])->save();

        $this->sendRestorePasswordConfirm($token, $input);

        $logger->save($logSecurity);

        return $account;
    }

    /**
     * Return confirm token and confirm token expired
     *
     * @param Token $token
     * @param array $input
     * @throws Error
     */
    public function sendRestorePasswordConfirm(Token $token, array $input)
    {
        Core_Logger::getInstance(__CLASS__)
            ->exception(['Implement {$0} for {$1}', [__FUNCTION__, get_class($this)]], __FILE__, __LINE__);
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

        $logSecurity = Log_Security::create([
            'account_class' => $accountModelClass,
            'account_key' => $account->getPkValue(),
            'widget_class' => get_class($accountForm),
            'action_class' => $accountForm->get('subject', get_class($this))
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

//        $logSecurity->set([
//            'error' => $e->getMessage(),
//            'exception' => \Ifacesoft\Ice\Core\Domain\Exception\Error::create(__METHOD__, 'Failed', $accountForm->get(), $e)->get()
//        ]);

        $logger->save($logSecurity);

        return $account;
    }

    /**
     * Return confirm token and confirm token expired
     *
     * @param Token $token
     * @param array $input
     * @throws Error
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

        $logSecurity = Log_Security::create([
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue(),
            'widget_class' => get_class($accountForm),
            'action_class' => $accountForm->get('subject', get_class($this))
        ]);

        /** @var User $user */
        $user = $account->fetchOne(User::class, '/active', true);

        if (!$user || !$user->isActive()) {
            $error = 'User is blocked or not found';

            $logSecurity->set(['error' => $error]);

            $logger->save($logSecurity);

            return $accountForm->getLogger()->exception([$error, [], $accountForm->getResource()], __FILE__, __LINE__);
        }

        /** @var Model_Account $account */
        $account = $account->set($accountData)->save();

        $logger->save($logSecurity);

        return $account;
    }
}