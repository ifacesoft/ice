<?php

namespace Ice\Action;

use Ebs\Model\Log_Security;
use Ice\Core;
use Ice\Core\Config;
use Ice\Core\DataSource;
use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\Security as Core_Security;
use Ice\Core\Model\Security_Account;
use Ice\Core\Model\Security_User;
use Ice\Core\Widget_Security;
use Ice\Helper\Logger;
use Ice\Helper\String;
use Ice\Model\Token;
use Ice\Core\Logger as Core_Logger;

abstract class Security extends Widget_Form_Event
{
    /**
     * @param Security_Account|Model $account
     * @param array $input
     * @return Security_Account|Model
     * @throws Exception
     */
    final protected function signIn(Security_Account $account, array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $log = Log_Security::create([
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($this)
        ]);

        if ($account->isExpired()) {
            $error = 'Account is expired';

            $log->set('error', $error);

            $securityForm->getLogger()->save($log);

            return $securityForm->getLogger()->exception([$error, [], $securityForm->getResource()], __FILE__, __LINE__);
        }

        $userModelClass = Config::getInstance(Core_Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '/active', true);

        if (!$user || !$user->isActive()) {
            $error = 'User is blocked or not found';

            $log->set('error', $error);

            $securityForm->getLogger()->save($log);

            return $securityForm->getLogger()->exception([$error, [], $securityForm->getResource()], __FILE__, __LINE__);
        }

        Core_Security::getInstance()->login($account);

        $securityForm->getLogger()->save($log);

        $this->getLogger()->save($log);

        return $account;
    }

    /**
     * Sing up by account
     *
     * @param array $accountData
     * @param array $input user defaults
     * @param DataSource|string|null $dataSource
     * @return Model|Security_Account
     * @throws \Exception
     */
    final protected function signUp(array $accountData, array $input, $dataSource = null)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        /** @var Security_Account|Model $account */
        $accountModelClass = $accountData['modelClass'];

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'form_class' => get_class($this)
        ]);

        /** @var DataSource $dataSource */
        $dataSource = DataSource::getInstance($dataSource);

        try {
            $dataSource->beginTransaction();

            $confirm = null;

            if ($securityForm->isConfirm()) {
                $accountData['token'] = Token::create([
                    '/' => md5(String::getRandomString()),
                    '/expired' => $securityForm->getConfirmationExpired(),
                    'modelClass' => $accountModelClass,
                    '/data' => ['account_expired' => $securityForm->getExpired()]
                ])->save();

                $confirm = [
                    'token' => $accountData['token'],
                    'input' => $input
                ];

                if ($securityForm->isConfirmRequired()) {
                    $accountData['/expired'] = '0000-00-00';
                    $input['/active'] = 0;
                } else {
                    $accountData['/expired'] = $securityForm->getConfirmationExpired();
                    $input['/active'] = 1;
                }
            } else {
                $accountData['/expired'] = $securityForm->getExpired();
                $input['/active'] = 1;
            }

            /** @var Security_User|Model $userModelClass */
            $userModelClass = Config::getInstance(Core_Security::getClass())->get('userModelClass');

            $accountData['user'] = $userModelClass::create($input)->save();

            /** @var Security_Account|Model $account */
            $account = $accountModelClass::create($accountData)->save();

            if ($confirm) {
                $this->sendRegisterConfirm($confirm['token'], $confirm['input']);
            }

            $dataSource->commitTransaction();
            
            $log->set('account_key', $account->getPkValue());
            $securityForm->getLogger()->save($log);
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction();

            $log->set('error', Logger::getMessage($e));
            $securityForm->getLogger()->save($log);

            throw $e;
        }

        return (!$securityForm->isConfirm() || ($securityForm->isConfirm() && !$securityForm->isConfirmRequired())) && $securityForm->isAutologin()
            ? $this->signIn($account, $input)
            : $account;
    }

    /**
     * Return confirm token and confirm token expired
     *
     * @param Token $token
     * @throws Exception
     */
    public function sendRegisterConfirm(Token $token, array $input)
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
    public function sendRestorePasswordConfirm(Token $token, array $input)
    {
        Core_Logger::getInstance(__CLASS__)
            ->exception(['Implement {$0} for {$1}', [__FUNCTION__, get_class($this)]], __FILE__, __LINE__);
    }

    /**
     * @param Token $token
     * @param array $input
     * @return Security_Account|Model
     */
    final protected function registerConfirm(Token $token, array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        /** @var Security_Account|Model $accountClass */
        $accountClass = $token->get('modelClass');

        $log = Log_Security::create([
            'account_class' => $accountClass,
            'form_class' => get_class($this)
        ]);

        $account = $accountClass::getSelectQuery(['/pk', 'user__fk'], ['token' => $token])->getModel();

        if (!$account) {
            $error = 'Account not found';

            $log->set('error', $error);

            $securityForm->getLogger()->save($log);

            return $securityForm->getLogger()->exception([$error, [], $securityForm->getResource()], __FILE__, __LINE__);
        }

        $log->set('account_key', $account->getPkValue());

        $securityForm->getLogger()->save($log);

        $tokenData = $token->get('/data');

        $account->set(['/expired' => $tokenData['account_expired'], 'token__fk' => null])->save();
        $token->remove(); //todo: удалять токены в екшине

        $userModelClass = Config::getInstance(Core_Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '*', true);

        $user->set(['/active' => 1])->save();

        $this->getLogger()->save($log);

        return $account;
    }

    /**
     * @param Security_Account|Model $account
     * @param $input
     * @return null
     * @throws Exception
     */
    final protected function restorePassword($account, $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $accountModelClass = get_class($account);

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($this)
        ]);

        if ($account->isExpired()) {
            $error = 'Account is expired';

            $log->set('error', $error);

            $securityForm->getLogger()->save($log);

            return $securityForm->getLogger()->exception([$error, [], $securityForm->getResource()], __FILE__, __LINE__);
        }

        $token = Token::create([
            '/' => md5(String::getRandomString()),
            '/expired' => $securityForm->getConfirmationExpired(),
            'modelClass' => $accountModelClass,
        ])->save();

        $account->set(['token' => $token])->save();

        $this->sendRestorePasswordConfirm($token, $input);

        $securityForm->getLogger()->save($log);

        $this->getLogger()->save($log);

        return $account;
    }

    final protected function changePassword($account, $accountData, $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $log = Log_Security::create([
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($this)
        ]);

        if ($account->isExpired()) {
            $error = 'Account is expired';

            $log->set('error', $error);

            $securityForm->getLogger()->save($log);

            return $securityForm->getLogger()->exception([$error, [], $securityForm->getResource()], __FILE__, __LINE__);
        }

        $userModelClass = Config::getInstance(Core_Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '/active', true);

        if (!$user || !$user->isActive()) {
            $error = 'User is blocked or not found';

            $log->set('error', $error);

            $securityForm->getLogger()->save($log);

            return $securityForm->getLogger()->exception([$error, [], $securityForm->getResource()], __FILE__, __LINE__);
        }

        /** @var Security_Account|Model $account */
        $account = $account->set($accountData)->save();
        
        $securityForm->getLogger()->save($log);

        $this->getLogger()->save($log);

        return $account;
    }
}