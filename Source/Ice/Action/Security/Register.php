<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Widget_Form_Security_Register;

/**
 * Class Security_Register
 *
 * @see     Ice\Core\Action
 * @see     Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author  dp <email>
 * @version 0
 * @since   0
 */
class Security_Register extends Action
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
                'security' => ['default' => 'Ice:LoginPassword'],
                'redirect' => ['default' => '/ice/security/login'],
                'url' => ['providers' => 'router']
            ],
            'output' => ['resource' => 'Ice:Resource/Security_Login'],
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
        /** @var Widget_Form_Security_Register $formClass */
        $formClass = Widget_Form_Security_Register::getClass($input['security']);

        return [
            'form' => $formClass::create($input['url'], Widget_Form_Submit::getClass(), 'Security_Login')
        ];
    }
}
