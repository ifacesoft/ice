<?php
namespace Ice\Action;

use Ice\Core;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Data;
use Ice\Data\Table;
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
class Data_Table extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard/file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Php',
        'inputValidators' => [
            'data' => 'Ice:Is_Data'
        ],
        'layout' => Emmet::PANEL_BODY,
    ];

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
        $rows = [];

        $columns = $input['data']->getColumns();

        $rows[] = Php::getInstance()->fetch(Data::getClass('Ice:Table_Row_Header'), ['columns' => $columns]);

        foreach ($input['data']->getRows() as $row) {
            $rowResult = [];

            foreach ($columns as $column) {
                $rowResult[] = Php::getInstance()->fetch(Data::getClass($column['template']), ['value' => $row[$column['name']]]);
            }

            $rows[] = Php::getInstance()->fetch(Data::getClass('Ice:Table_Row_Data'), ['columns' => $rowResult]);
        }

        return ['rows' => $rows];
    }
}