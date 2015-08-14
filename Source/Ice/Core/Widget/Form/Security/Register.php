<?php

namespace Ice\Core;

abstract class Widget_Form_Security_Register extends Widget_Form_Security
{
    /**
     * Register by input form data
     *
     * @param array $user User defaults
     * @return Model|Security_Account
     */
    abstract public function register(array $user = []);
}