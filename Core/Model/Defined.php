<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 28.12.13
 * Time: 23:42
 */

namespace ice\core\model;

use ice\core\Config;
use ice\core\Model;

class Defined extends Model
{
    public static function getDefinedConfig()
    {
        return Config::get(get_called_class(), array(), 'Defined', true);
    }
} 