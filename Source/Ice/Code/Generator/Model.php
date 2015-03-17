<?php
/**
 * Ice code generator implementation model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Class_Generator;
use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Model as Core_Model;
use Ice\Core\Module;
use Ice\Helper\Arrays;
use Ice\Helper\File;
use Ice\Helper\Object;
use Ice\View\Render\Php;
use Ice\Helper\Model as Helper_Model;
use Ice\Helper\Php as Helper_Php;

/**
 * Class Model
 *
 * Model code generator
 *
 * @see Ice\Core\Code_Generator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Code_Generator
 */
class Model extends Code_Generator
{
    /**
     * Generate code and other
     *
     * @param $class
     * @param array $data Sended data requered for generate
     * @param bool $force Force if already generate
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function generate($class, $data, $force = false)
    {
        $namespace = Object::getNamespace(Core_Model::getClass(), $class);

        $fieldNames = Arrays::column($data['columns'], 'fieldName');

        $module = Module::getInstance(Object::getModuleAlias($class));

        $path = $module->get(Module::SOURCE_DIR);

        if ($namespace) {
            $path .= 'Model/';
        }

        $filePath = Loader::getFilePath($class, '.php', $path, false, true, true);

        $isFileExists = file_exists($filePath);

        if (!$force && $isFileExists) {
            Code_Generator::getLogger()->info(['Model {$0} already created', $class]);
            return;
        }

        if ($isFileExists) {
            Class_Generator::create($class, Core_Model::getClass())->generate($data);
            return;
        }

        $data = [
            'fields' => $fieldNames,
            'namespace' => rtrim($namespace, '\\'),
            'modelName' => Object::getName($class),
            'config' => str_replace("\n", "\n\t\t", Helper_Php::varToPhpString($data, false))
        ];

        $classString = Php::getInstance()->fetch(__CLASS__, $data);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Model {$0} recreated'
            : 'Model {$0} created';

        if ($isFileExists) {
            Code_Generator::getLogger()->info([$message, $class], Logger::SUCCESS);
        }

        Loader::load($class);
    }
}