<?php

namespace Ice\Widget;

class Html_Ul_Li extends Html_Div
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }
}