<?php
/**
 * Ice code generator implementation validator class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Validator as Core_Validator;
use Ice\Helper\File;
use Ice\Helper\Object;
use Ice\Render\Php;

/**
 * Class Validator
 *
 * Validator code generator
 *
 * @see Ice\Core\Code_Generator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Code_Generator
 */
class Validator extends Code_Generator
{
    /**
     * Generate code and other
     *
     * @param  array $data Sended data requered for generate
     * @param  bool $force Force if already generate
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.0
     */
    public function generate(array $data = [], $force = false)
    {
        $class = $this->getInstanceKey();

        //        $class = Object::getClass(Core_Validator::getClass(), $data);
        $namespace = Object::getNamespace(Core_Validator::getClass(), $class);

        $module = Module::getInstance(Object::getModuleAlias($class));

        $path = $module->get(Module::SOURCE_DIR);

        //        if ($namespace) {
        //            $path .= 'Class/';
        //        }

        $filePath = $path . str_replace(['\\', '_'], '/', $class) . '.php';

        $isFileExists = file_exists($filePath);

        if (!$force && $isFileExists) {
            Code_Generator::getLogger()->info(['Validator {$0} already created', $class]);
            return;
        }

        $data = [
            'namespace' => rtrim($namespace, '\\'),
            'validatorName' => Object::getClassName($class)
        ];

        $classString = Php::getInstance()->fetch(__CLASS__, $data);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Validator {$0} recreated'
            : 'Validator {$0} created';

        if ($isFileExists) {
            Code_Generator::getLogger()->info([$message, $class], Logger::SUCCESS);
        }

        Loader::load($class);
    }

    /**
     * Init object
     *
     * @param array $params
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected function init(array $params)
    {
        // TODO: Implement init() method.
    }
}
