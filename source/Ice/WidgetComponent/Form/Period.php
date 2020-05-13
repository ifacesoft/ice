<?php

namespace Ice\WidgetComponent;

class Form_Period extends Form_Date
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }
}