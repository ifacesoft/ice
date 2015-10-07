<?php
/**
 * Ice core resource class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */
namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Cacher;
use Ice\Exception\FileNotFound;
use Ice\Helper\Api_Client_Yandex_Translate;
use Ice\Helper\File;
use Ice\Helper\Json;
use Ice\Helper\String;
use Ice\Render\Replace;

/**
 * Class Resource
 *
 * Core resource (localizations) class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Resource implements Cacheable
{
    use Stored;

    /**
     * Localized resources
     *
     * @var array
     */
    private $resource = null;

    /**
     * Target class
     *
     * @var Core
     */
    private $class = null;

    /**
     * Private constructor for resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Return localized resource by key
     *
     * @param  $message
     * @param  $params
     * @param  $class
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($message, $params = null, $class = null)
    {
        if ($class) {
            return Resource::create($class)->get($message, $params);
        }

        $locale = Request::locale();

        $cacher = Cacher::getInstance($this->class, __CLASS__);
        $key = $locale . '/' . crc32(Json::encode([$message, $params]));

        if ($localizedMessage = $cacher->get($key)) {
            return $localizedMessage;
        }

        if (!isset($this->resource[$message])) {
            $this->resource[$message] = [];
        }

        if (!isset($this->resource[$message][$locale])) {
            $this->resource[$message][$locale] = $this->set(rtrim($message, ';'));
        }

        return $cacher->set(
            $key,
            Replace::getInstance()->fetch(
                $this->resource[$message][$locale],
                (array)$params,
                null,
                Render::TEMPLATE_TYPE_STRING
            )
        );
    }

    /**
     * Create new instance of resource
     *
     * @param  $class
     * @return Resource
     * @throws FileNotFound
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public static function create($class)
    {
        $repository = Resource::getRepository($class);

        if ($resource = $repository->get('resource')) {
            return $resource;
        }

        $resource = new Resource();

        $resource->resource = Resource::getResources($class);
        $resource->class = $class;

        return $repository->set('resource', $resource);
    }

    private static function getResources($class)
    {
        $resources = [];

        $resourceFiles = Loader::getFilePath($class, '.res.php', Module::RESOURCE_DIR, false, true, false, true);

        foreach ($resourceFiles as $resourceFile) {
            if ($resourceFromFile = File::loadData($resourceFile)) {
                $resources += $resourceFromFile;
            }
        }

        return $resources;
    }

    public function set($message)
    {
        if (empty($message)) {
//            $e = new \Exception();
//            print_r($e->getTraceAsString());die();

            Logger::getInstance(__CLASS__)->warning('Empty resource message', __FILE__, __LINE__);

            return $message;
        }

        $oldData = Resource::getResources($this->class);

        $data = [];

        $data[$message] = isset($oldData[$message])
            ? $oldData[$message]
            : [];

        try {
            $from = Api_Client_Yandex_Translate::detect($message);

            if (!isset($data[$message][$from])) {
                $data[$message][$from] = $message;
            }

            $requestConfig = Config::getInstance(Request::getClass());

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
            Logger::getInstance(__CLASS__)->exception('error', __FILE__, __LINE__, $e);
            $data[$message][Request::locale()] = $message;
        }

        $moduleResourceFile =
            Module::getInstance()->get(Module::RESOURCE_DIR) . str_replace(['\\', '_'], '/', $this->class) . '.res.php';

        $moduleResourceData = file_exists($moduleResourceFile)
            ? File::loadData($moduleResourceFile)
            : [];

        File::createData($moduleResourceFile, array_merge($moduleResourceData, array_diff_key($data, $moduleResourceData)));

        return $message;
    }

    /**
     * Validate cacheable object
     *
     * @param  $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function validate($value)
    {
        return $value;
    }

    /**
     * Invalidate cacheable object
     *
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function invalidate()
    {
        // TODO: Implement invalidate() method.
    }

    /**
     * @return string
     */
    public function getResourceClass()
    {
        return $this->class;
    }
}
