<?php
/**
 * Ice action model delete class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Query;

/**
 * Class Model_Delete
 *
 * Action for delete options
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 */
class Model_Form_Delete extends Form_Submit
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'input' => [
                'modelClassName' => ['validators' => 'Ice:Not_Empty'],
                'pk' => ['validators' => 'Ice:Numeric_Positive']
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public function run(array $input)
    {
//        return Query::getBuilder(Model::getClass($input['modelClassName']))
//            ->deleteQuery($input['pk'])
//            ->getQueryResult()
//            ->getAffectedRows()
//            ? ['success' => $this->getLogger()->info('Delete successfully', Logger::SUCCESS)]
//            : ['error' => $this->getLogger()->info('Delete failed', Logger::DANGER)];
    }
}
