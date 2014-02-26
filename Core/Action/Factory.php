<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 27.10.13
 * Time: 15:44
 */

namespace ice\core\action;

interface Factory
{
    public static function getDelegate($delegateName);
}