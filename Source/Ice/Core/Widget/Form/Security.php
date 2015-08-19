<?php

namespace Ice\Core;

use Ice\Helper\Date;
use Ice\Helper\String;

abstract class Widget_Form_Security extends Widget_Form
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
            return Widget_Form_Security::getLogger()
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
    protected function signIn(Security_Account $account)
    {
        if (!$account || $account->isExpired()) {
            return Widget_Form_Security::getLogger()
                ->exception(
                    ['Account is expired', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, '/active', true);

        if (!$user || !$user->isActive()) {
            return Widget_Form_Security::getLogger()
                ->exception(
                    ['User is blocked', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        Security::getInstance()->login($account);

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
    protected function signUp($accountModelClass, array $accountData, array $userData = [], $dataSource = null)
    {
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

        /** @var Data_Source $dataSource */
        $dataSource = Data_Source::getInstance($dataSource);

        try {
            $dataSource->beginTransaction();

            /** @var Security_User|Model $userModelClass */
            $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

            $accountData['user'] = $userModelClass::create($userData)->save();
            $account = $accountModelClass::create($accountData)->save();

            $dataSource->commitTransaction();

            return (!$this->confirm || ($this->confirm && !$this->confirmRequired)) && $this->autologin
                ? $this->signIn($account)
                : $account;
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction();

            return Widget_Form_Security::getLogger()
                ->exception(
                    ['Sign up failed', [], $this->getResource()],
                    __FILE__,
                    __LINE__,
                    $e
                );
        }
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
        $accountData['confirmation_token'] =  md5(String::getRandomString());
        $accountData['confirmation_expired'] = $this->getConfirmationExpired();

        return $accountData;
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
