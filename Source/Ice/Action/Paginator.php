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
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'input' => [
                'fastStep' => ['default' => 5],
                'params' => ['default' => []],
                'data' => ['validators' => 'Ice:Is_Query_Result'],
                'actionClassName' => ['validators' => 'Ice:Not_Null']
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function run(array $input)
    {
        /** @var Query_Result $data */
        $data = $input['data'];

        $output = [];

        list($output['page'], $output['limit'], $output['foundRows']) = $data->getQuery()->getPagination();

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