<?php
namespace Ice\Action;

use Ice\Core;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Data as Core_Data;
use Ice\Helper\Arrays;
use Ice\Helper\Emmet;
use Ice\View\Render\Php;

/**
 * Class Data_Table
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.2
 * @since 0.1
 */
class Data extends Action
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
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => Emmet::PANEL_BODY],
            'input' => [
                'default' => [
                    'data' => ['validators' => 'Ice:Is_Data']
                ]
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Core_Data $form */
        $data = $input['data'];

        $rows = [];

        $columns = $data->getColumns();

        $filterFields = $data->getFilterFields();

        $rows[] = Php::getInstance()->fetch(Data::getClass('Ice:Table_Row_Header'), ['columns' => array_intersect_key($columns, array_intersect(Arrays::column($columns, 'name'), $filterFields))]);

        foreach ($data->getRows() as $row) {
            $rowResult = [];

            foreach ($columns as $column) {
                if (!in_array($column['name'], $filterFields)) {
                    continue;
                }

                $rowResult[] = Php::getInstance()->fetch(Data::getClass($column['template']), ['value' => $row[$column['name']], 'scheme' => $column]);
            }

            $rows[] = Php::getInstance()->fetch(Data::getClass('Ice:Table_Row_Data'), ['columns' => $rowResult]);
        }

        return ['rows' => $rows];
    }
}