<?php

namespace Ice\Core;

use Ice\Helper\Date;

abstract class Widget_Form_Security extends Widget_Form
{
    /**
     * Is login after success sign up
     *
     * @var bool
     */
    private $autologin = false;

    /**
     * Redirect url
     *
     * If is null use referrer url
     *
     * @var string|null
     */
    private $redirect = null;

    /**
     * Timeout redirect after success registration
     *
     * @var int
     */
    private $redirectTimeout = 0;

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
     * @param Security_Account $account
     * @param array $user user defaults
     * @return Model|Security_Account
     */
    protected function signUp(Security_Account $account, array $user = [])
    {
        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        if ($this->confirm) {
            $account->set($this->sendConfirm());

            if ($this->confirmRequired) {
                $account->set(['/expired' => '0000-00-00']);
                $user['/active'] = 0;
            } else {
                $account->set(['/expired' => Date::get(strtotime($this->confirmExpired))]);
                $user['/active'] = 1;
            }
        } else {
            $account->set(['/expired' => Date::get(strtotime($this->expired))]);
            $user['/active'] = 1;
        }

        /** @var Data_Source $dataSource */
        $dataSource = $account->getDataSource();
        try {
            $dataSource->beginTransaction();

            $account = $account->set([
                'user' => $userModelClass::create($user)->save()
            ])->save();

            $dataSource->commitTransaction();
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction();
        }

        if ((!$this->confirm || ($this->confirm && !$this->confirmRequired)) && $this->autologin) {
            return $this->signIn($account);
        }

        return $account;
    }

    /**
     * Return confirm token and confirm token expired
     *
     * @return array
     */
    public function sendConfirm() {
        return Logger::getInstance('Ice\App')
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
     * @param string $redirect
     * @param int $redirectTimeout
     * @return Widget_Form_Security
     */
    public function setRedirect($redirect, $redirectTimeout = 0)
    {
        $this->redirect = $redirect;
        $this->setRedirectTimeout($redirectTimeout);

        return $this;
    }

    /**
     * @param int $redirectTimeout
     * @return Widget_Form_Security
     */
    public function setRedirectTimeout($redirectTimeout)
    {
        $this->redirectTimeout = $redirectTimeout;
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

    /**
     * @return null|string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return int
     */
    public function getRedirectTimeout()
    {
        return $this->redirectTimeout;
    }
}
