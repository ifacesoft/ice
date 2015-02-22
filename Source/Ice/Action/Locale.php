<?php namespace Ice\Action;


use Ice\Core\Action;
use Ice\Core\Request;
use Ice\Data\Provider\Router;
use Ice\Exception\Redirect;
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
            'view' => ['template' => ''],
            'input' => [
                Router::DEFAULT_DATA_PROVIDER_KEY => [
                    'locale' => ['default' => '']
                ]
            ]
        ];
    }

    /** Run action
     *
     * @param array $input
     * @return array
     * @throws Redirect
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function run(array $input)
    {
        if (in_array($input['locale'], Api_Client_Yandex_Translate::getLocales())) {
            $_SESSION['locale'] = $input['locale'];
        }

        if (Request::referer()) {
            throw new Redirect(Request::referer());
        }

        throw new Redirect('/');


    }
}