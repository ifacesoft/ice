<?php

namespace Ice\Helper;

use Ice\Core\Config as Core_Config;
use Ice\Core\Request as Core_Request;
use Ice\DataProvider\Repository;

class Api_Client_Yandex_Translate
{
    const URL = 'https://translate.yandex.net/api/v1.5/tr.json/';

    /**
     * Map of locale and country
     *
     * @var array
     */
    public static $localeCountryMapping = [
        'az' => 'az',
        'be' => 'be',
        'bg' => 'bg',
        'ca' => 'ca',
        'cs' => 'cz',
        'da' => 'dk',
        'de' => 'de',
        'el' => 'gr',
        'en' => 'gb',
        'es' => 'es',
        'et' => 'et',
        'fi' => 'fi',
        'fr' => 'fr',
        'hr' => 'hr',
        'hu' => 'hu',
        'hy' => 'am',
        'it' => 'it',
        'lt' => 'lt',
        'lv' => 'lv',
        'mk' => 'mk',
        'nl' => 'nl',
        'no' => 'no',
        'pl' => 'pl',
        'pt' => 'pt',
        'ro' => 'ro',
        'sk' => 'sk',
        'sl' => 'sl',
        'sq' => 'al',
        'sr' => 'sr',
        'sv' => 'sv',
        'tr' => 'tr',
        'uk' => 'ua',
    ];

    /**
     * Return pi_Client_Yandex_Translate class
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getClass()
    {
        return __CLASS__;
    }

    /**
     * Определение языка
     *
     * @param  $text
     * @return mixed
     * @throws \Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function detect($text)
    {
        $yaKey = Core_Config::getInstance(__CLASS__)->get('translateKey');

        if (empty($yaKey)) {
            return Core_Config::getInstance(Core_Request::class)->get('locale');
        }

        $url = Api_Client_Yandex_Translate::URL . 'detect?key=' . $yaKey . '&text=' . $text;

        if ($detect = Json::decode(Http::getContents($url))['lang']) {
            return $detect;
        }

        return Core_Config::getInstance(Core_Request::class)->get('locale');
    }

    /**
     * Перевод текста
     *
     * @param  $text
     * @param  $direction
     * @return mixed
     * @throws \Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function translate($text, $direction)
    {
        $yaKey = Core_Config::getInstance(__CLASS__)->get('translateKey');

        if (empty($yaKey)) {
            return $text;
        }

        $url = Api_Client_Yandex_Translate::URL . 'translate?key=' . $yaKey . '&text=' . urlencode($text) . '&lang=' . $direction;

        if ($translate = Json::decode(Http::getContents($url))['text'][0]) {
            return $translate;
        }

        return $text;
    }

    /**
     * Return flags info
     *
     * @param  null $locale
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getFlags($locale = null)
    {
        $flags = [];

        foreach (Api_Client_Yandex_Translate::getLocales($locale) as $lang => $locale) {
            $flags[] = [
                'lang' => $lang,
                'locale' => $locale,
                'country' => isset(Api_Client_Yandex_Translate::$localeCountryMapping[$locale])
                    ? Api_Client_Yandex_Translate::$localeCountryMapping[$locale]
                    : $locale
            ];
        }

        return $flags;
    }

    /**
     * Get available locales
     *
     * @param  null $locale
     * @return array
     * @throws \Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getLocales($locale = null)
    {
        if (!$locale) {
            $locale = Core_Config::getInstance(Core_Request::class)->get('locale');
        }

        $locales = [];

        foreach (Api_Client_Yandex_Translate::getLangs($locale) as $lang => $direction) {
            $locales[$lang] = substr($direction, strlen($locale . '_'));
        }

        return $locales;
    }

    /**
     * Получение списка направлений перевода
     *
     * @param  null $locale
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getLangs($locale)
    {
        $repository = Repository::getInstance(__CLASS__);
        $key = 'langs/' . $locale;

        if ($langs = $repository->get($key)) {
            return $langs;
        }

        $yaKey = Core_Config::getInstance(__CLASS__)->get('translateKey');

        if (empty($yaKey)) {
            return [];
        }

        $requestLocale = Config::getInstance(Core_Request::getClass())->get('locale');

        $url = Api_Client_Yandex_Translate::URL . 'getLangs?key=' . $yaKey . '&ui=' . $requestLocale;

        $langs = Json::decode(Http::getContents($url));

        $directions = [$langs['langs'][$locale] => $locale . '_' . $locale];

        foreach ($langs['dirs'] as $direction) {
            if (String::startsWith($direction, $locale)) {
                $directions[$langs['langs'][substr($direction, strlen($locale . '_'))]] = $direction;
            }
        }

        if ($directions = $repository->set($key, $directions)) {
            return $directions;
        }

        return [];
    }
}
