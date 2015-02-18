<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 28.12.14
 * Time: 20:32
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Data\Provider\Request;

class Test extends Action
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
                Request::DEFAULT_DATA_PROVIDER_KEY => [
                    'test' => ['default' => 'test'],
                ]
            ],
            'actions' => [
                '_NoView',
                ['_Smarty', ['inputTestSmarty' => 'inputTestSmarty']],
                ['_Twig', ['inputTestTwig' => 'inputTestTwig']],
                ['_Php', ['inputTestPhp' => 'inputTestPhp1'], 'firstPhp'],
                ['_Php', ['inputTestPhp' => 'inputTestPhp2']]
            ]
        ];
    }

    /** Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        return [
            'test' => $input['test'],
        ];
    }
}