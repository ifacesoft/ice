<?php
/**
 * Ice code generator implementation model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Model as Core_Model;
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
     * @param array $data Sended data requered for generate
     * @param bool $force Force if already generate
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function generate($data, $force = false)
    {
        $modelClass = Helper_Model::getModelClassByTableName($data['scheme']['tableName']);

        $namespace = Object::getNamespace(Core_Model::getClass(), $modelClass);

        $fieldNames = Arrays::column($data['columns'], 'fieldName');

        $path = $namespace ? 'Source/' : 'Source/Model/';

        $filePath = Loader::getFilePath($modelClass, '.php', $path, false, true, true);

        $isFileExists = file_exists($filePath);

        if (!$force && $isFileExists) {
            Code_Generator::getLogger()->info(['Model {$0} already created', $modelClass]);
            return;
        }

        $data = [
            'fields' => $fieldNames,
            'namespace' => rtrim($namespace, '\\'),
            'modelName' => Object::getName($modelClass),
            'config' => str_replace("\n", "\n\t\t", Helper_Php::varToPhpString($data, false))
        ];

        $classString = Php::getInstance()->fetch(__CLASS__, $data);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Model {$0} recreated'
            : 'Model {$0} created';

        if ($isFileExists) {
            Code_Generator::getLogger()->info([$message, $modelClass], Logger::SUCCESS);
        }

        Loader::load($modelClass);
    }
}