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
 * @todo WARNING!!! ONLY TEST USAGE
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since   0.0
 */
class Model_Delete extends Action
{
    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'cache' => ['ttl' => -1, 'count' => 1000],
     *      'roles' => []
     *  ];
     * ```
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
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
        return Query::getBuilder(Model::getClass($input['modelClassName']))
            ->deleteQuery($input['pk'])
            ->getQueryResult()
            ->getAffectedRows()
            ? ['success' => Model_Delete::getLogger()->info('Delete successfully', Logger::SUCCESS)]
            : ['error' => Model_Delete::getLogger()->info('Delete failed', Logger::DANGER)];
    }
}
