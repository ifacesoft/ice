<?php

namespace Ice\Helper;

use Ice\Core\Config as Core_Config;
use Ice\Data\Provider\Repository;

class Api_Yandex
{
    /**
     * Получение списка направлений перевода
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function getLangs()
    {
        $repository = Repository::getInstance(__CLASS__);

        if ($langs = $repository->get('langs')) {
            return $langs;
        }

        $yaKey = Core_Config::create(__CLASS__)->get('translateKey');

        if (empty($yaKey)) {
            throw new \Exception('Key is empty. See https://tech.yandex.ru/keys/get/?service=trnsl');
        }

        if ($langs = $repository->set('langs', Json::decode(Http::getContents('https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=' . $yaKey))['dirs'])) {
            return $langs;
        }

        throw new \Exception('Fail getLangs');
    }

    public static function detect($text)
    {
        $yaKey = Core_Config::create(__CLASS__)->get('translateKey');

        if (empty($yaKey)) {
            throw new \Exception('Key is empty. See https://tech.yandex.ru/keys/get/?service=trnsl');
        }

        if ($detct = Json::decode(Http::getContents('https://translate.yandex.net/api/v1.5/tr.json/detect?key=' . $yaKey . '&text=' . $text))['lang']) {
            return $detct;
        }

        throw new \Exception('Fail detect');
    }

    public static function translate($text, $lang)
    {
        $yaKey = Core_Config::create(__CLASS__)->get('translateKey');

        if (empty($yaKey)) {
            throw new \Exception('Key is empty. See https://tech.yandex.ru/keys/get/?service=trnsl');
        }
        if ($translate = Json::decode(Http::getContents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $yaKey . '&text=' . $text . '&lang=' . $lang))['text'][0]) {
            return $translate;
        }
        throw new \Exception('Fail translate');
    }
}