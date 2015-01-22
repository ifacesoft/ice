<?php
/**
 * Ice action paginator class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Query_Result;
use Ice\Helper\Arrays;

/**
 * Class Paginator
 *
 * Default ice paginator
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since 0.0
 */
class Paginator extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'defaultViewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'defaultViewRenderClassName' => 'Ice:Php',
        'inputDefaults' => [
            'fastStep' => 5,
            'params' => []
        ],
        'inputValidators' => [
            'data' => 'Ice:Is_Query_Result',
            'actionClassName' => 'Ice:Not_Null'
        ],
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
     * @version 0.0
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Query_Result $data */
        $data = $input['data'];

        $output = [];

        list($output['page'], $output['limit'], $output['foundRows']) = $data->getPagination();

        if ($output['page'] > 1) {
            $output['first'] = 1;
        }

        if ($output['page'] - $input['fastStep'] >= 1) {
            $output['fastPrev'] = $output['page'] - $input['fastStep'];
        }

        if ($output['page'] - 2 >= 1) {
            $output['before2'] = $output['page'] - 2;
        }

        if ($output['page'] - 1 >= 1) {
            $output['prev'] = $output['page'] - 1;
            $output['before1'] = $output['page'] - 1;
        }

        $pageCount = intval($output['foundRows'] / $output['limit']) + 1;

        if ($output['page'] == $pageCount) {
            $output['limit'] = $output['foundRows'] - ($pageCount - 1) * $output['limit'];
        }

        if ($output['page'] + 1 <= $pageCount) {
            $output['next'] = $output['page'] + 1;
            $output['after1'] = $output['page'] + 1;
        }

        if ($output['page'] + 2 <= $pageCount) {
            $output['after2'] = $output['page'] + 2;
        }

        if ($output['page'] + $input['fastStep'] <= $pageCount) {
            $output['fastNext'] = $output['page'] + $input['fastStep'];
        }

        if ($output['page'] < $pageCount) {
            $output['last'] = $pageCount;
        }

        $output['actionClassName'] = $input['actionClassName'];
        $output['params'] = Arrays::toJsObjectString($input['params']);

        return $output;
    }
}