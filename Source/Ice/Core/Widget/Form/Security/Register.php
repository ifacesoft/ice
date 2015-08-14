<?php

namespace Ice\Core;

abstract class Widget_Form_Security_Register extends Widget_Form_Security
{
    /**
     * Register by input form data
     *
     * @return Security_Account|Model
     */
    public abstract function register();
}