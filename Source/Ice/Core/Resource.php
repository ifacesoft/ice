<?php
/**
 * Ice core resource class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */
namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\File_Not_Found;
use Ice\Helper\Api_Client_Yandex_Translate;
use Ice\Helper\File;
use Ice\Helper\String;
use Ice\View\Render\Replace;

/**
 * Class Resource
 *
 * Core resource (localizations) class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class Resource
{
    use Core;

    /**
     * Localized resources
     *
     * @var array
     */
    private $_resource = null;

    /**
     * Target class
     *
     * @var Core
     */
    private $_class = null;

    /**
     * Private constructor for resource
     *
     * @param $class
     * @param $resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($class, $resource)
    {
        $this->_resource = $resource;
        $this->_class = $class;
    }

    /**
     * Create new instance of resource
     *
     * @param $class
     * @return Resource
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo need caching
     * @version 0.0
     * @since 0.0
     */
    public static function create($class)
    {
        $resourceFile = Loader::getFilePath($class, '.res.php', Module::RESOURCE_DIR, false);

        return $resourceFile
            ? new Resource($class, File::loadData($resourceFile))
            : new Resource($class, []);
    }

    /**
     * Return localized resource by key
     *
     * @param $message
     * @param $params
     * @param $class
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($message, $params = null, $class = null)
    {
        /** @var string $message */
        /** @var Core $class */
        $resource = isset($class)
            ? Resource::create($class)->_resource
            : $this->_resource;

        if (!isset($class)) {
            $class = $this->_class;
        }

        $locale = Request::locale();

        if (!isset($resource[$message]) || !isset($resource[$message][$locale])) {
            $resource = Resource::create($class);
            $resource->set(rtrim($message, ';'));
            $resource = $resource->_resource;
        }

        if (isset($resource[$message][$locale])) {
            $message = $resource[$message][$locale];
        }

        return Replace::getInstance()->fetch($message, (array)$params, View_Render::TEMPLATE_TYPE_STRING);
    }

    public function set($message)
    {
        $resourceFile = Loader::getFilePath($this->_class, '.res.php', Module::RESOURCE_DIR, false, true, true);

        $data = file_exists($resourceFile)
            ? File::loadData($resourceFile)
            : [];

        if (!isset($data[$message])) {
            $data[$message] = [];
        }

        try {
            $from = Api_Client_Yandex_Translate::detect($message);

            if (!isset($data[$message][$from])) {
                $data[$message][$from] = $message;
            }

            $requestConfig = Request::getConfig();

            if ($requestConfig->get('multiLocale')) {
                foreach (Api_Client_Yandex_Translate::getLangs($from) as $lang) {
                    if (String::startsWith($lang, $from)) {
                        $to = substr($lang, strlen($from . '_'));

                        if (!isset($data[$message][$to])) {
                            $data[$message][$to] = Api_Client_Yandex_Translate::translate($message, $lang);
                        }
                    }
                }
            } else {
                $locale = $requestConfig->get('locale');
                if ($locale != $from) {
                    $direction = $from . '-' . $locale;
                    $data[$message][$locale] = in_array($direction, Api_Client_Yandex_Translate::getLangs($from))
                        ? Api_Client_Yandex_Translate::translate($message, $direction)
                        : $message;
                }
            }
        } catch (\Exception $e) {
            Resource::getLogger()->exception('error', __FILE__, __LINE__, $e);
            $data[$message][Request::locale()] = $message;
        }

        File::createData($resourceFile, $data);

        return $message;
    }
}