<?php

namespace Ice\Widget;

class Html_Table_Tr_Td extends Html_Span
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