<?php

namespace Ice\Widget;

use Ice\Core\Model_Account;
use Ice\Helper\Date;

abstract class Account_Form extends Form
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
    private $confirmExpired = '+3 days';

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
    private $expired = '+1 years';

    private $accountModelClass = null;

    private $attachAccount = false;

    private $updateUserDataOnAttachAccount = false;

    private $updateUserEmailOnAttachAccount = false;

    private $successOnExists = false;

    /**
     * Prolongation callback
     *
     * @var string
     */
    private $prolongate = false;

    /**
     * @return mixed
     */
    public function getProlongate()
    {
        return $this->prolongate;
    }

    /**
     * @param boolean $prolongate
     * @return $this
     */
    public function setProlongate($prolongate = true)
    {
        $this->prolongate = $prolongate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAttachAccount()
    {
        return $this->attachAccount;
    }

    /**
     * @param bool $attachAccount
     * @return Account_Form
     */
    public function setAttachAccount($attachAccount = true)
    {
        $this->attachAccount = $attachAccount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isUpdateUserDataOnAttachAccount()
    {
        return $this->updateUserDataOnAttachAccount;
    }

    /**
     * @return boolean
     */
    public function isUpdateUserEmailOnAttachAccount()
    {
        return $this->updateUserEmailOnAttachAccount;
    }

    /**
     * @param boolean $updateUserDataOnAttachAccount
     *
     * @return $this
     */
    public function setUpdateUserDataOnAttachAccount($updateUserDataOnAttachAccount = true)
    {
        $this->updateUserDataOnAttachAccount = $updateUserDataOnAttachAccount;

        return $this;
    }

    /**
     * @param boolean $updateUserEmailOnAttachAccount
     *
     * @return $this
     */
    public function setUpdateUserEmailOnAttachAccount($updateUserEmailOnAttachAccount = true)
    {
        $this->updateUserEmailOnAttachAccount = $updateUserEmailOnAttachAccount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessOnExists()
    {
        return $this->successOnExists;
    }

    /**
     * @param bool $successOnExists
     * @return Account_Form
     */
    public function setSuccessOnExists($successOnExists = true)
    {
        $this->successOnExists = $successOnExists;

        return $this;
    }

    /**
     * @return Model_Account
     */
    public function getAccountModelClass()
    {
        return $this->accountModelClass;
    }

    /**
     * @param Model_Account|string $accountModelClass
     * @return $this
     */
    public function setAccountModelClass($accountModelClass)
    {
        $this->accountModelClass = $accountModelClass;
        return $this;
    }

    public function getConfirmationExpired()
    {
        return Date::get($this->confirmExpired);
    }

    public function getExpired()
    {
        return Date::get($this->expired);
    }

    /**
     * @param string $expired
     * @return $this
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutologin()
    {
        return $this->autologin;
        return (!$this->isConfirm() || ($this->isConfirm() && !$this->isConfirmRequired())) && $this->autologin;
    }

    /**
     * @param boolean $autologin
     * @return $this
     */
    public function setAutologin($autologin = true)
    {
        $this->autologin = $autologin;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param boolean $confirm
     * @param string $confirmExpired
     * @param bool $confirmRequired
     * @return $this
     */
    public function setConfirm($confirm = true, $confirmExpired = '+1 hours', $confirmRequired = true)
    {
        $this->confirm = $confirm;

        $this->setConfirmExpired($confirmExpired);
        $this->setConfirmRequired($confirmRequired);

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
     * @return boolean
     */
    public function isConfirmRequired()
    {
        return $this->confirmRequired;
    }

    /**
     * @param boolean $confirmRequired
     */
    public function setConfirmRequired($confirmRequired)
    {
        $this->confirmRequired = $confirmRequired;
    }

    /**
     * @return Model_Account
     */
    abstract public function getAccount();
}
