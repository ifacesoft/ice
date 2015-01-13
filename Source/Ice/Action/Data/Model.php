<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Logger;
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
        'defaultViewRenderClassName' => 'Ice:Smarty',
        'inputValidators' => [
            'modelClassName' => 'Ice:Not_Empty',
            'formFilterFields' => 'Ice:Not_Empty',
            'dataFilterFields' => 'Ice:Not_Empty',
        ],
        'inputDefaults' => [
            'page' => 1,
            'limit' => 10,
            'submitActionName' => 'Ice:Form_Submit',
            'reRenderClosest' => 'Ice:Data_Model',
            'reRenderActionNames' => [],
            'grouping' => 0,
        ]
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
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

        $actionContext->addAction(
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
        );

        $queryResult = $modelClass::query()
            ->setPaginator([$input['page'], $input['limit']])
            ->desc('/pk')
            ->select('*');

        $actionContext->addAction(
            'Ice:Paginator', [
                'data' => $queryResult,
                'actionClassName' => 'Ice:Data_Model',
                'params' => $params
            ]
        );

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

        $actionContext->addAction('Ice:Data', ['data' => $data]);

        return [
            'actionClassName' => 'Ice:Data_Model'
        ];
    }
}