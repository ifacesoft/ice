<?php
/**
 * Ice bootstrap class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Date;

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

        Loader::init($data['loader'], !empty($data['force']));
        Logger::init();

        $this->init();
    }



    /**
     * @return string
     */
    public function getModuleConfigPath()
    {
        return $this->moduleConfigPath;
    }

    private function init()
    {
        // todo: устанавливать также как и таймзону (см. ниже)
        setlocale(LC_ALL, 'en_US.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        date_default_timezone_set(Date::getServerTimezone());
    }
}
