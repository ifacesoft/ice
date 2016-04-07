<?php
/**
 * Ice bootstrap class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Composer\Autoload\ClassLoader;
use Ice\Core;

/**
 * Class Bootstrap
 *
 * Initialization required components for Ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 */
class Bootstrap extends Container
{
    use Core;

    private $moduleConfigPath = null;

    protected function __construct(array $data)
    {
        parent::__construct($data);

        setlocale(LC_ALL, 'en_US.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        date_default_timezone_set('UTC');

        Loader::init($data['loader'], !empty($data['force']));
        Logger::init();
    }

    /**
     * @return string
     */
    public function getModuleConfigPath()
    {
        return $this->moduleConfigPath;
    }
}
