<?php

namespace Ice\Core;

use Ice\Model\User;
use PHPUnit_Framework_TestCase;

class ModelTest extends  PHPUnit_Framework_TestCase {
    public function testActiveRecordCrud()
    {
        User::createTable();
    }
}
