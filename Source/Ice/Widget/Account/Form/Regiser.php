<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 13.10.16
 * Time: 18:01
 */

namespace Ice\Widget;


abstract class Account_Form_Register extends Account_Form
{
    private $updateUserOnAddAccount = true;

    /**
     * @return boolean
     */
    public function isUpdateUserOnAddAccount()
    {
        return $this->updateUserOnAddAccount;
    }

    /**
     * @param boolean $updateUserOnAddAccount
     *
     * @return $this
     */
    public function setUpdateUserOnAddAccount($updateUserOnAddAccount)
    {
        $this->updateUserOnAddAccount = $updateUserOnAddAccount;

        return $this;
    }
}