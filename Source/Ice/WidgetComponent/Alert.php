<?php

namespace Ice\WidgetComponent;

class Alert extends Html_Div
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

}