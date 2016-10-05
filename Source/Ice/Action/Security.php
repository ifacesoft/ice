<?php

namespace Ice\Action;

use Ice\Core\Config;
use Ice\Core\DataSource;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Model;
use Ice\Core\Model\Security_User;
use Ice\Core\Model_Account;
use Ice\Core\Security as Core_Security;
use Ice\Exception\Security_Account_Login;
use Ice\Exception\Security_Account_Register;
use Ice\Helper\Logger;
use Ice\Helper\String;
use Ice\Model\Account;
use Ice\Model\Log_Security;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

abstract class Security extends Widget_Form_Event
{
    /**
     * Sing up by account
     *
     * @param Account_Form $accountForm
     * @return Model_Account
     * @throws \Exception
     */
    final protected function signUp(Account_Form $accountForm)
    {
        $logger = $this->getLogger();

        /** @var Model_Account $accountModelClass */
        $accountModelClass = $accountForm->getAccountModelClass();

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'form_class' => get_class($accountForm)
        ]);

        /** @var Model_Account $account */
        $account = null;

        try {
            $account = $accountModelClass::create();

            $account = $account->signUp($accountForm);

            if (!$account) {
                throw new Security_Account_Register('Account register fail');
            }

            $log->set('account_key', $account->getPkValue());

            $logger->save($log);
        } catch (\Exception $e) {
            $log->set('error', Logger::getMessage($e));

            $logger->save($log);

            throw $e;
        }

        return $account;
    }

    /**
     * @param Model_Account $account
     * @param Account_Form $accountForm
     * @return Model_Account
     * @throws \Exception
     */
    final protected function signIn(Model_Account $account, Account_Form $accountForm)
    {
        $logger = $this->getLogger();

        $log = Log_Security::create([
            'form_class' => get_class($accountForm),
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue()
        ]);

        try {
            $account = $account->signIn($accountForm);

            if (!$account) {
                throw new Security_Account_Login('Account login fail');
            }

            $logger->save($log);
        } catch (\Exception $e) {
            $log->set('error', Logger::getMessage($e));

            $logger->save($log);

            throw $e;
        }

        return $account;
    }

    /**
     * @param Token $token
     * @param array $input
     * @return Model_Account
     */
    final protected function registerConfirm(Token $token, array $input)
    {
        $logger = $this->getLogger();

        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        /** @var Model_Account $accountClass */
        $accountClass = $token->get('modelClass');

        $log = Log_Security::create([
            'account_class' => $accountClass,
            'form_class' => get_class($accountForm)
        ]);

        /** @var Model_Account $account */
        $account = $accountClass::getSelectQuery(['/pk', 'user__fk'], ['token' => $token])->getModel();

        if (!$account) {
            $error = 'Account not found';

            $log->set('error', $logger->info($error, Core_Logger::DANGER, true));

            $logger->save($log);

            return $accountForm->getLogger()->exception([$error, [], $accountForm->getResource()], __FILE__, __LINE__);
        }

        $log->set('account_key', $account->getPkValue());

        $logger->save($log);

        $tokenData = $token->get('/data');

        $account->set(['/expired' => $tokenData['account_expired'], 'token__fk' => null])->save();
        $token->remove(); //todo: удалять токены в екшине

        $userModelClass = Config::getInstance(Core_Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '*', true);

        $user->set(['/active' => 1])->save();

        $logger->save($log);

        return $account;
    }

    /**
     * @param Model_Account $account
     * @param $input
     * @return null
     * @throws Exception
     */
    final protected function restorePassword($account, $input)
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

        $token = Token::create([
            '/' => md5(String::getRandomString()),
            '/expired' => $accountForm->getConfirmationExpired(),
            'modelClass' => $accountModelClass,
        ])->save();

        $account->set(['token' => $token])->save();

        $this->sendRestorePasswordConfirm($token, $input);

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

        $userModelClass = Config::getInstance(Core_Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '/active', true);

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