<?php

namespace Ice\Widget;

use Ice\Core\Model_Account;
use Ice\Helper\Date;
use Ice\Model\Account;

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
    public function setProlongate($prolongate)
    {
        $this->prolongate = $prolongate;

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
    public function setConfirm($confirm, $confirmExpired = '+1 hours', $confirmRequired = true)
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
    public function getAccountModelClass()
    {
        return $this->accountModelClass;
    }

    /**
     * @param Model_Account $accountModelClass
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
     * @return boolean
     */
    public function isAutologin()
    {
        return $this->autologin;
    }

    /**
     * @param boolean $autologin
     * @return $this
     */
    public function setAutologin($autologin)
    {
        $this->autologin = $autologin;

        return $this;
    }
}
