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
use Ice\Helper\File;
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
 *
 * @version 0.0
 * @since 0.0
 */
class Resource extends Factory
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
     * @param null $hash
     * @return Resource
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($class, $hash = null)
    {
        $resourceFile = Loader::getFilePath($class, '.res.php', 'Resource/', false);

        return $resourceFile
            ? new Resource($class, File::loadData($resourceFile))
            : new Resource($class, []);
    }

    /**
     * Return localized resource by key
     *
     * @param $message
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($message)
    {
        $params = [];

        if (is_array($message)) {
            switch (count($message)) {
                case 2:
                    list($message, $params) = $message;
                    break;
                case 3:
                    list($message, $params, $class) = $message;
                    break;
                default:
                    break;
            }
        }

        /** @var string $message */
        /** @var Core $class */
        $resource = isset($class)
            ? $class::getResource()->_resource
            : $this->_resource;

        if (!isset($class)) {
            $class = $this->_class;
        }

        if (empty($resource[$message])) {
            $resource = self::update($message, $class);
        }

        $locale = Request::locale();

        if (isset($resource[$message][$locale])) {
            $message = $resource[$message][$locale];
        }

        return Replace::getInstance()->fetch($message, (array)$params, View_Render::TEMPLATE_TYPE_STRING);
    }

    /**
     * Add to resource file new resource message
     *
     * @param $message
     * @param $class
     * @return mixed
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private static function update($message, $class)
    {
        $resourceFile = Loader::getFilePath($class, '.res.php', 'Resource/', false, true);

        $data = file_exists($resourceFile)
            ? File::loadData($resourceFile)
            : [];

        $data[$message] = [
            Request::locale() => $message
        ];

        return File::createData($resourceFile, $data);
    }
}