<?php

namespace Ice\Bootstrap;

use Ice\Core\Bootstrap;

class Symfony extends Bootstrap
{
    protected function __construct(array $data)
    {
        $data['force'] = true;

        parent::__construct($data);

//        set_error_handler('Ice\Core\Logger::errorHandler');
        register_shutdown_function('session_write_close');
    }

    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }
}
