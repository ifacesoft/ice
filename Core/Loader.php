<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 24.12.13
 * Time: 23:02
 */

namespace ice\core;

use ice\Exception;
use ice\Ice;

class Loader
{
    private static $_autoLoaders = array();

    public static function load($class)
    {
        if (class_exists($class)) {
            return;
        }

        /** @var Data_Provider $dataProvider */
        $dataProvider = Data_Provider::getInstance(Ice::getConfig()->getParam('loaderDataProviderKey'));

        $fileName = $dataProvider->get($class);
        if ($fileName) {
            require_once $fileName;
            return;
        }

        $fileName = self::getFilePath($class, 'Core', '.php');

//        if (function_exists('fb')) {
//            fb($fileName);
//        }

        if ($fileName) {
            $dataProvider->set($class, $fileName);
            require_once $fileName;
        }
    }

    /**
     * Return class path
     *
     * @param $class
     * @param $type
     * @param $ext
     * @param bool $isRequired
     * @param bool $isNotNull
     * @param bool $isOnlyFirst
     * @throws Exception
     * @return null|string
     */
    public static function getFilePath(
        $class,
        $type,
        $ext,
        $isRequired = true,
        $isNotNull = false,
        $isOnlyFirst = false
    )
    {
        $fileName = null;

        $stack = array();

        $extClass = explode(':', $class);
        if (count($extClass) == 2) {
            list($type, $class) = $extClass;
        }

        $modulesConfigName = Ice::getConfig()->getConfigName() . ':modules';

        foreach (Ice::getConfig()->getParams('modules') as $module) {
            $filePath = '';

            $moduleConfig = new Config($module, $modulesConfigName);

            foreach (explode('\\', $class) as $filePathPart) {
                $filePathPart[0] = strtoupper($filePathPart[0]);
                $filePath .= '/' . $filePathPart;
            }

            $filePath = str_replace('_', '/', $filePath);
//
            $modulePath = $moduleConfig->getParam('path');

            $isLegacy = !strpos(ltrim($class, '\\'), '\\') || $type == 'Config';

            if ($isLegacy) {
                if ($type == 'Core') {
                    $type = 'Class';
                }

                $fileName = $modulePath . $type . $filePath . $ext;
            } else {
                $modulePath = substr($modulePath, 0, strrpos(substr($modulePath, 0, -1), '/'));
                $fileName = $modulePath . $filePath . $ext;
            }

            $stack[] = $fileName;

            if (file_exists($fileName)) {
                return $fileName;
            }

            if ($isOnlyFirst || !$isLegacy) {
                break;
            }
        }

        if ($isRequired) {
            throw new Exception('Не удалось найти путь до файла "' . $type . ':' . $class . '"', $stack);
        }

//        fb($stack);

        return $isNotNull ? reset($stack) : null;
    }

    /**
     * @desc Подключение автозагрузки классов
     */
    public static function register($autoLoader)
    {
        foreach (self::$_autoLoaders as $loader) {
            spl_autoload_unregister($loader);
        }

        $autoLoaders = self::$_autoLoaders;
        array_unshift($autoLoaders, $autoLoader);
        self::$_autoLoaders = $autoLoaders;

        foreach (self::$_autoLoaders as $loader) {
            spl_autoload_register($loader);
        }
    }

    /**
     * @desc Отключение автозагрузки классов
     */
    public static function unregister($autholoader)
    {
        foreach (self::$_autoLoaders as $key => $loader) {
            if ($loader == $autholoader) {
                spl_autoload_unregister($autholoader);
                unset(self::$_autoLoaders[$key]);
                break;
            }
        }
    }
} 