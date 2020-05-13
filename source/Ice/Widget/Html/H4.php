<?php

namespace Ice\Widget;

class Html_H4 extends Html_Div
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

}