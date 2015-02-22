<?php namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Data_Scheme;
use Ice\Core\Data_Source;

class Orm_Scheme_Update extends Action
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
                'default' => [
                    'force' => ['default' => 0]
                ]
            ]
        ];
    }

    /** Run action
     *
     * @param array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @package Ice
     * @subpackage Action
     *
     * @version 0.5
     * @since 0.5
     */
    public function run(array $input)
    {
        $output = [];

        foreach (Data_Source::getConfig()->gets() as $dataSourceClass => $config) {
            foreach ($config as $key => $schemes) {
                foreach ((array)$schemes as $scheme) {
                    $output[$key . '.' . $scheme] =
                        Data_Scheme::create($dataSourceClass . '/' . $key . '.' . $scheme)->update($input['force']);
                }
            }
        }

        return $output;
    }
}