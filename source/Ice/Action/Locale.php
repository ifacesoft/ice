<?php namespace Ice\Action;

use Ice\Core\Action;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;
use Ice\DataProvider\Session;
use Ice\Exception\Http_Redirect;
use Ice\Helper\Api_Client_Yandex_Translate;

class Locale extends Action
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
                'locale' => ['providers' => [Router::class, Request::class, Session::class], 'default' => ''],
                'referer' => ['providers' => Request::class, 'default' => '/']
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     * @throws Http_Redirect
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        if (in_array($input['locale'], Api_Client_Yandex_Translate::getLocales())) {
            Session::getInstance()->set(['locale' => $input['locale']]);
        }

        throw new Http_Redirect($input['referer']);
    }
}
