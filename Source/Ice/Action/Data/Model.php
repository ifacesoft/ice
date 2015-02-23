<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Model;
use Ice\Helper\Arrays;

/**
 * Class Data_Model
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author dp <email>
 * @version 0
 * @since 0
 */
class Data_Model extends Action
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
            'view' => ['viewRenderClass' => 'Ice:Smarty'],
            'input' => [
                'modelClassName' => ['validators' => 'Ice:Not_Empty'],
                'formFilterFields' => ['validators' => 'Ice:Not_Empty'],
                'dataFilterFields' => ['validators' => 'Ice:Not_Empty'],
                'page' => ['default' => 1],
                'limit' => ['default' => 10],
                'submitActionName' => ['default' => 'Ice:Form_Submit'],
                'reRenderClosest' => ['default' => 'Ice:Data_Model'],
                'reRenderActionNames' => ['default' => []],
                'grouping' => ['default' => 0],
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Model $modelClass */
        $modelClass = Model::getClass($input['modelClassName']);

        $params = [
            'modelClassName' => $input['modelClassName'],
            'formFilterFields' => $input['formFilterFields'],
            'dataFilterFields' => $input['dataFilterFields'],
            'submitActionName' => $input['submitActionName'],
            'reRenderClosest' => $input['reRenderClosest'],
            'reRenderActionNames' => $input['reRenderActionNames'],
        ];

        $submitTitle = Data_Model::getResource()->get('Save') . ' ' . $modelClass::getTitle();

        $this->addAction([
            'Ice:Form_Model',
            [
                'modelClassName' => $input['modelClassName'],
                'pk' => 0,
                'submitActionName' => $input['submitActionName'],
                'formFilterFields' => $input['formFilterFields'],
                'reRenderClosest' => $input['reRenderClosest'],
                'reRenderActionNames' => $input['reRenderActionNames'],
                'grouping' => $input['grouping'],
                'submitTitle' => $submitTitle,
                'template' => '_Modal',
                'params' => $params
            ]
        ]);

        $queryResult = $modelClass::query()
            ->setPaginator([$input['page'], $input['limit']])
            ->desc('/pk')
            ->select('*');

        $this->addAction([
            'Ice:Paginator', [
                'data' => $queryResult,
                'actionClassName' => 'Ice:Data_Model',
                'params' => $params
            ]
        ]);

        $pkName = $modelClass::getPkFieldName();

        $data = $modelClass::getData($input['dataFilterFields'])
            ->button(
                $pkName,
                '<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>',
                [
                    'modelClassName' => $input['modelClassName'],
                    'submitActionName' => $input['submitActionName'],
                    'formFilterFields' => Arrays::toJsArrayString($input['formFilterFields']),
                    'grouping' => $input['grouping'],
                    'submitTitle' => $submitTitle,
                    'template' => '_Modal',
                    'params' => Arrays::toJsObjectString($params),
                    'reRenderClosest' => $input['reRenderClosest'],
                    'reRenderActionNames' => Arrays::toJsArrayString($input['reRenderActionNames']),
                ],
                'Ice:Table_Column_Button_Edit'
            )
            ->button(
                $pkName,
                '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>',
                [
                    'modelClassName' => $input['modelClassName'],
                    'params' => Arrays::toJsObjectString($params),
                    'reRenderClosest' => $input['reRenderClosest'],
                    'reRenderActionNames' => Arrays::toJsArrayString($input['reRenderActionNames']),
                ],
                'Ice:Table_Column_Button_Remove'
            )
            ->bind($queryResult->getRows());

        $this->addAction(['Ice:Data', ['data' => $data]]);

        return [
            'actionClassName' => 'Ice:Data_Model'
        ];
    }
}