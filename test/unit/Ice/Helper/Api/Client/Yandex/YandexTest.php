<?php namespace Ice\Helper;

use Ice\Core\Config as Core_Config;
use PHPUnit\Framework\TestCase;

class Api_Client_Yandex_TranslateTest extends TestCase
{

    public function testGetLangs()
    {
        if (!empty(Core_Config::getInstance(Api_Client_Yandex_Translate::getClass())->get('translateKey'))) {
            $this->assertTrue(!empty(Api_Client_Yandex_Translate::getLangs('ru')));
        }
    }

    public function testDetect()
    {
        if (!empty(Core_Config::getInstance(Api_Client_Yandex_Translate::getClass())->get('translateKey'))) {
            $this->assertEquals('ru', Api_Client_Yandex_Translate::detect('Определение языка'));
        }
    }

    public function testTranslate()
    {
        if (!empty(Core_Config::getInstance(Api_Client_Yandex_Translate::getClass())->get('translateKey'))) {
            $this->assertEquals('The language definition', Api_Client_Yandex_Translate::translate('Определение языка', 'ru-en'));
        }
    }

    public function testGetLocales()
    {
        if (!empty(Core_Config::getInstance(Api_Client_Yandex_Translate::getClass())->get('translateKey'))) {
            $this->assertTrue(count(Api_Client_Yandex_Translate::getLocales('ru')) >= 33);
        }
    }

    public function testGetFlags()
    {
        if (!empty(Core_Config::getInstance(Api_Client_Yandex_Translate::getClass())->get('translateKey'))) {
            $flags = Api_Client_Yandex_Translate::getLocales('ru');
            $this->assertTrue(count($flags) >= 33);
        }
    }
}