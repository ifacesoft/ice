<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Model;
use Ice\Core\Request;
use Ice\Widget\Menu\Pagination;

/**
 * Class Data_Model
 *
 * @see     Ice\Core\Action
 * @see     Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author  dp <email>
 * @version 0
 * @since   0
 */
class Crud extends Action
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
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'input' => [
                'page' => ['dataProviderKey' => 'request', 'default' => 1],
                'limit' => ['default' => 15],
                'modelClassName' => ['validators' => 'Ice:Not_Empty'],
//                'formFilterFields' => ['validators' => 'Ice:Not_Empty'],
//                'dataFilterFields' => ['validators' => 'Ice:Not_Empty'],
//                'page' => ['default' => 1],
//                'limit' => ['default' => 10],
//                'submitActionName' => ['default' => 'Ice:Form_Submit'],
//                'reRenderClosest' => ['default' => 'Ice:Data_Model'],
//                'reRenderActionNames' => ['default' => []],
//                'grouping' => ['default' => 0],
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = Model::getClass($input['modelClassName']);

        $tableData = $modelClass::getTableData(Request::uri(true), __CLASS__);
        $paginationMenu = Pagination::create(Request::uri(true), __CLASS__);

        $modelClass::createQueryBuilder()
            ->attachWidget('tableData', $tableData)
            ->attachWidget('paginationMenu', $paginationMenu)
            ->getSelectQuery('*')
            ->getQueryResult();

        return [
            'tableData' => $tableData,
            'paginationMenu' => $paginationMenu
        ];
    }
}
