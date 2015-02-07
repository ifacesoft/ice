<?php namespace Ice\Helper;

use Ice\Core\Logger;
use PHPUnit_Framework_TestCase;
use Ice\Helper\Api_Yandex as Helper_Api_Yandex;
use Ice\Core\Config as Core_Config;

class Api_YandexTest extends PHPUnit_Framework_TestCase
{

    public function testGetLangs()
    {
        if (!empty(Core_Config::getInstance('Ice\Helper\Api_Yandex')->get('translateKey'))) {
            $this->assertTrue(!empty(Helper_Api_Yandex::getLangs()));
        }
    }

    public function testDetect()
    {
        if (!empty(Core_Config::getInstance('Ice\Helper\Api_Yandex')->get('translateKey'))) {
            $this->assertEquals('ru', Helper_Api_Yandex::detect('Определение языка'));
        }
    }

    public function testTranslate()
    {
        if (!empty(Core_Config::getInstance('Ice\Helper\Api_Yandex')->get('translateKey'))) {
            $this->assertEquals('The language definition', Helper_Api_Yandex::translate('Определение языка', 'ru-en'));
        }
    }
}