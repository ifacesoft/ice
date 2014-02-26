<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 09.01.14
 * Time: 23:10
 */

namespace ice\action;

use ice\core\action\Cliable;
use ice\core\Action;
use ice\core\Action_Context;

class Cache_Flush extends Action implements Cliable
{
    protected $config = array('Apc', 'Redis');

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
//        foreach ($this->getConfig()->getParams('dataProviders') as $dataProviderKey) {
//            Data_Provider::getInstance($dataProviderKey)->flushAll();
//        }
    }
}