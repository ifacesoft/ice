<?php

namespace Ice\Widget;

class Demo_Vendor_Facebook_Login extends Block
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;
        $config['output']['appId'] = '121933101594886';

        return $config;
    }

    protected function build(array $input)
    {
        return parent::build($input);
    }
}