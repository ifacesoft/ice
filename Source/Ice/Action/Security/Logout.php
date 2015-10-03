<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Request;
use Ice\Core\View;
use Ice\Exception\Redirect;

/**
 * Class Security_Logout
 *
 * @see     Ice\Core\Action
 * @see     Ice\Core\Action_Context;
 * @package Ice\Action;
 *
 * @author dp <email>
 *
 * @version 0
 * @since   0
 */
class Security_Logout extends Action
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
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'redirect' => ['providers' => 'request', 'default' => true]
            ]
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     * @throws Redirect
     */
    public function run(array $input)
    {
        session_destroy();

        return ['redirect' => $input['redirect'] === true ? Request::referer() : $input['redirect']];
    }
}
