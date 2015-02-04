<?php

namespace Ice\Data\Source;

use PHPUnit_Framework_TestCase;

class MongoTest extends PHPUnit_Framework_TestCase
{
    public function testMongo()
    {
        $mongodb = Mongodb::getInstance()->getConnection();

        $this->assertInstanceOf('MongoClient', $mongodb);
    }
}