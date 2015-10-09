<?php

namespace Ice\Core;

use Ebs\Model\Log_Security;
use Ice\Helper\Date;
use Ice\Helper\Logger as Helper_Logger;
use Ice\Widget\Form;

abstract class Widget_Form_Security extends Form
{
    /**
     * Is login after success sign up
     *
     * @var bool
     */
    private $autologin = false;

    /**
     * Confirmation is needed
     *
     * @var bool
     */
    private $confirm = false;

    /**
     * Confirm token expired time
     *
     * In strtotime format
     * For example '+1 hour' or '+10 hours'
     *
     * @var string
     */
    private $confirmExpired = '+3 hours';

    /**
     * Block user without confirm
     *
     * @var bool
     */
    private $confirmRequired = true;

    /**
     * Account expired time
     *
     * In strtotime format
     * For example '+1 year' or '+10 hours'
     *
     * @var string
     */
    private $expired = '+100 years';

    private $accountModelClass = null;

    /**
     * @return Security_Account
     */
    public function getAccountModelClass()
    {
        return $this->accountModelClass;
    }

    /**
     * @param Security_Account $accountModelClass
     * @return Widget_Form_Security
     */
    public function setAccountModelClass($accountModelClass)
    {
        $this->accountModelClass = $accountModelClass;
        return $this;
    }

    public function validate()
    {
        try {
            return parent::validate();
        } catch (\Exception $e) {
            return $this->getLogger()
                ->exception(
                    ['Validation failure', [], $this->getResource()],
                    __FILE__,
                    __LINE__,
                    $e
                );
        }
    }

    /**
     * @param Security_Account|Model $account
     * @return null|string
     * @throws Exception
     */
    final public function signIn(Security_Account $account)
    {
        $log = Log_Security::create([
            'account_class' => get_class($account),
            'account_key' => $account->getPkValue(),
            'form_class' => get_class($this)
        ]);

        if ($account->isExpired()) {
            $error = 'Account is expired';

            $log->set('error', $error);

            $this->getLogger()->save($log);

            return $this->getLogger()->exception([$error, [], $this->getResource()], __FILE__, __LINE__);
        }

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '/active', true);

        if (!$user || !$user->isActive()) {
            $error = 'User is blocked or not found';

            $log->set('error', $error);

            $this->getLogger()->save($log);

            return $this->getLogger()->exception([$error, [], $this->getResource()], __FILE__, __LINE__);
        }

        $this->getLogger()->save($log);

        Security::getInstance()->login($account);

        $this->getLogger()->save($log);

        return $account;
    }

    /**
     * Sing up by account
     *
     * @param Security_Account|Model $accountModelClass
     * @param array $accountData
     * @param array $userData user defaults
     * @param Data_Source|string|null $dataSource
     *
     * @return Model|Security_Account
     */
    final public function signUp($accountModelClass, array $accountData, array $userData, $dataSource = null)
    {
        $account = null;

        $log = Log_Security::create([
            'account_class' => $accountModelClass,
            'form_class' => get_class($this)
        ]);

        /** @var Data_Source $dataSource */
        $dataSource = Data_Source::getInstance($dataSource);

        try {
            if ($this->confirm) {
                $accountData = $this->sendConfirm($accountData);

                if ($this->confirmRequired) {
                    $accountData['/expired'] = '0000-00-00';
                    $userData['/active'] = 0;
                } else {
                    $accountData['/expired'] = $this->getConfirmationExpired();
                    $userData['/active'] = 1;
                }
            } else {
                $accountData['/expired'] = $this->getExpired();
                $userData['/active'] = 1;
            }

            $dataSource->beginTransaction();

            /** @var Security_User|Model $userModelClass */
            $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

            $accountData['user'] = $userModelClass::create($userData)->save();
            $account = $accountModelClass::create($accountData)->save();

            $dataSource->commitTransaction();

            $log->set('account_key', $account->getPkValue());
            $this->getLogger()->save($log);
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction();

            $log->set('error', Helper_Logger::getMessage($e));
            $this->getLogger()->save($log);

            return $this->getLogger()->exception(['Sign up failed', [], $this->getResource()], __FILE__, __LINE__, $e);
        }

        return (!$this->confirm || ($this->confirm && !$this->confirmRequired)) && $this->autologin
            ? $this->signIn($account)
            : $account;

    }

    /**
     * Return confirm token and confirm token expired
     *
     * @param array $accountData
     * @return array
     * @throws Exception
     */
    public function sendConfirm(array $accountData)
    {
        return Logger::getInstance(__CLASS__)
            ->exception(['Implement {$0} for {$1}', [__FUNCTION__, get_class($this)]], __FILE__, __LINE__);
    }

    /**
     * @param boolean $autologin
     * @return Widget_Form_Security
     */
    public function setAutologin($autologin)
    {
        $this->autologin = $autologin;
        return $this;
    }

    /**
     * @param boolean $confirm
     * @param string $confirmExpired
     * @param bool $confirmRequired
     * @return Widget_Form_Security
     */
    public function setConfirm($confirm, $confirmExpired = '+3 hours', $confirmRequired = true)
    {
        $this->confirm = $confirm;

        $this->setConfirmExpired($confirmExpired);
        $this->setConfirmRequired($confirmRequired);

        return $this;
    }

    /**
     * @param string $expired
     * @return Widget_Form_Security
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
        return $this;
    }

    /**
     * @param string $confirmExpired
     */
    public function setConfirmExpired($confirmExpired)
    {
        $this->confirmExpired = $confirmExpired;
    }

    /**
     * @param boolean $confirmRequired
     */
    public function setConfirmRequired($confirmRequired)
    {
        $this->confirmRequired = $confirmRequired;
    }

    protected function getConfirmationExpired()
    {
        return Date::get(strtotime($this->confirmExpired));
    }

    protected function getExpired()
    {
        return Date::get(strtotime($this->expired));
    }
}
