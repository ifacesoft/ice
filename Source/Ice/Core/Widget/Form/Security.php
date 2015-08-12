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
}
