<?php
/**
 * Ice core container abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\DataProvider\Session;
use Ice\Exception\FileNotFound;
use Ice\Helper\Object;

/**
 * Class Container
 *
 * Core container abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Container
{
    private $instanceKey = null;

    /**
     * Container constructor.
     * @param array $data
     */
    protected function __construct(array $data)
    {
        $this->instanceKey = $data['instanceKey'];
    }

    /**
     * Get instance from container
     *
     * @param  string $instanceKey
     * @param  null $ttl
     * @param array $params
     * @return Container|Core
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getInstance($instanceKey, $ttl = null, array $params = [])
    {
        /** @var Container|Core $class */
        $class = get_called_class();

        /** @var Container|Core $baseClass */
        $baseClass = $class::getBaseClass();

        if (is_object($instanceKey) && $instanceKey instanceof $baseClass) {
            return $instanceKey;
        }

        if ($class == $baseClass) {
            if (!$instanceKey) {
                return $baseClass::getInstance(Config::getInstance($class)->get('defaultClassName'), $ttl, $params);
            } elseif (is_string($instanceKey)) {
                $parts = explode('/', $instanceKey);

                if (count($parts) == 1) {
                    $class = $instanceKey;
                    $instanceKey = 'default';
                } else {
                    $class = $parts[0];
                    $instanceKey = $parts[1];
                }

                $class = Object::getClass($baseClass, $class);

                return $class::getInstance($instanceKey, $ttl, $params);
            }
        }

//        $startTime = Profiler::getMicrotime();
//        $startMemory = Profiler::getMemoryGetUsage();

        if (!$instanceKey || $instanceKey == 'default') {
            $instanceKey = $class::getDefaultKey();
        }

        $logger = Logger::getInstance(__CLASS__);

        $object = null;
        try {
            $dataProvider = $class::getDataProvider('instance');

            if ($ttl != -1 && $object = $dataProvider->get($instanceKey)) {
//                $message = $class . ' - ' . print_r($instanceKey, true);

//                Profiler::setPoint($message, $startTime, $startMemory);
//                Logger::log(Profiler::getReport($message), 'container (cache - ' . $dataProviderClassName .  ')', 'LOG');
                return $object;
            }

            $params['instanceKey'] = $instanceKey;

            if ($object = $class::create($params)) {
//                $message = $class . ' - ' . print_r($instanceKey, true);

//                Profiler::setPoint($message, $startTime, $startMemory);
                if ($ttl == -1) {
//                    Logger::log(Profiler::getReport($message), 'container (not cache)', 'WARN');
                } else {
//                    Logger::log(Profiler::getReport($message), 'container (new - ' . $dataProviderClassName .  ')', 'INFO');
                    $dataProvider->set([$instanceKey => $object], $ttl);
                }
            }

        } catch (FileNotFound $e) {
//            $message = $class . ' - ' . print_r($instanceKey, true);
//            Profiler::setPoint($message, $startTime, $startMemory);
//            Logger::log(Profiler::getReport($message), 'container (error)', 'Error');

            if ($baseClass == Code_Generator::getClass()) {
                $logger->exception(['Code generator for {$0} not found', $instanceKey], __FILE__, __LINE__, $e);
            }

            if (Environment::getInstance()->isDevelopment()) {
                $baseClass::getCodeGenerator($instanceKey)->generate($params);
                $object = $class::create($instanceKey);
            } else {
                $logger->error(['File {$0} not found', $instanceKey], __FILE__, __LINE__, $e);
            }

        }

        return $object;
    }

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected static function getDefaultKey()
    {
        Logger::getInstance(__CLASS__)->exception(
            ['Implementation {$0} is required for {$1}', [__FUNCTION__, get_called_class()]],
            __FILE__,
            __LINE__
        );

        return null;
    }

    /**
     * Create instance
     *
     * @param  $params
     * @return Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.4
     */
    final private static function create(array $params)
    {
        $class = get_called_class();
        return new $class($params);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return Logger::getInstance(get_class($this));
    }

    public function removeInstance()
    {
        /** @var Container|Core $class */
        $class = get_class($this);

        $class::getDataProvider('instance')->delete($this->getInstanceKey());
    }

    /**
     * @return string
     */
    public function getInstanceKey()
    {
        return $this->instanceKey;
    }

    protected function getDataProviderSession($index = 'default')
    {
        return Session::getInstance(get_class($this), $index);
    }
}
