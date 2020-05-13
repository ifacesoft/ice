<?php

namespace Ice\Widget;

use Ice\Core\Widget;

class Html_Div extends Widget
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

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }
}