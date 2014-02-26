<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.02.14
 * Time: 20:47
 */

namespace ice\Action;

use ice\core\Action;
use ice\core\action\Cliable;
use ice\core\Action_Context;

class Deploy extends Action implements Cliable
{
    protected $staticActions = array(
        'ice\action\Data_Mapping_Sync',
        'ice\action\Model_Scheme_Sync',
        'ice\action\Model_Defined_Sync'
    );

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        // TODO: Implement run() method.
    }
}