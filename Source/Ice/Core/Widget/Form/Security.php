<?php

namespace Ice\Core;

abstract class Widget_Form_Security extends Widget_Form
{
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
    protected function authenticate(Security_Account $account)
    {
        if ($account->isExpired()) {
            return Widget_Form_Security::getLogger()
                ->exception(
                    ['Account is expired', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $account->fetchOne($userModelClass, ['/active', '/expired'], true);

        if (!$user->isActive()) {
            return Widget_Form_Security::getLogger()
                ->exception(
                    ['User is blocked', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        if ($user->isExpired()) {
            return Widget_Form_Security::getLogger()
                ->exception(
                    ['User is expired', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        return $user->getPkValue();
    }
}
