<?php
/**
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Helper\File;
use Ice\Render\External_PHPExcel;
use Ice\Render\Php;

/**
 * Class External_PHPExcel
 *
 * View code generator for PHPExcel templates
 *
 * @see Ice\Core\Code_Generator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Code_Generator
 */
Class Render_External_PHPExcel extends Code_Generator
{
    /**
     * Generate code and other
     *
     * @param  array $data Sended data requered for generate
     * @param  bool $force Force if already generate
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function generate(array $data = [], $force = false)
    {
        $class = $this->getInstanceKey();

        $filePath = Loader::getFilePath($class, External_PHPExcel::TEMPLATE_EXTENTION, Module::RESOURCE_DIR, false, true, true);

        $isFileExists = file_exists($filePath);

        if (!$force && $isFileExists) {
            $this->getLogger()->info(['Template {$0} {$1} already created', ['PHPExcel', $class]]);
            return '';
        }

        $classString = Php::getInstance()->fetch(__CLASS__, ['class' => $class]);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Template {$0} {$1}" recreated'
            : 'Template {$0} {$1}" created';

        if ($isFileExists) {
            $this->getLogger()->info([$message, ['PHPExcel', $class]], Logger::SUCCESS);
        }

        return $classString;
    }

    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    protected function init(array $data)
    {
        // TODO: Implement init() method.
    }
}
