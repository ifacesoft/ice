<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Model;

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
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Smarty',
        'inputValidators' => [
            'modelName' => 'Ice:Not_Empty',
        ],
        'inputDefaults' => [
            'page' => 1,
            'limit' => 5,
            'formFilterFields' => [],
            'dataFilterFields' => [],
            'submitActionName' => 'Ice:Form_Submit',
            'reRenderActionNames' => ['Bi:Cabinet'],
            'groupping' => 0,
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
        $modelClass = Model::getClass($input['modelName']);

        $actionContext->addAction(
            'Ice:Form_Model',
            [
                'modelName' => $input['modelName'],
                'pk' => 0,
                'submitActionName' => $input['submitActionName'],
                'reRenderActionNames' => $input['reRenderActionNames'],
                'filterFields' => $input['formFilterFields'],
                'groupping' => 0,
                'submitTitle' => Data_Model::getResource()->get('Add') . ' ' . $modelClass::getTitle(),
                'template' => '_Modal'
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
                'params' => [
                    'modelName' => $input['modelName'],
                    'formFilterFields' => $input['formFilterFields'],
                    'dataFilterFields' => $input['dataFilterFields'],
                    'submitActionName' => $input['submitActionName'],
                    'reRenderActionNames' => $input['reRenderActionNames'],
                ]
            ]
        );

        $data = $modelClass::getData($input['dataFilterFields'])
            ->button('blog_pk', 'Изменить', [
                'onclick' => 'Ice_Form.modal(\'Bi:Blog\', 1, \'Ice:Form_Submit\', [\'Bi:Cabinet\'], [\'blog_name\'], 0, \'Add blog\', \'_Modal\'); return false;',
                'type' => 'info',
                'icon' => 'edit'
            ])
            ->button('blog_pk', 'Удалить', [
                'onclick' => 'Ice_Form.modal(\'Bi:Blog\', 1, \'Ice:Form_Submit\', [\'Bi:Cabinet\'], [\'blog_name\'], 0, \'Add blog\', \'_Modal\'); return false;',
                'type' => 'danger',
                'icon' => 'remove'
            ])
            ->bind($queryResult->getRows());

        $actionContext->addAction('Ice:Data', ['data' => $data]);

        return [
            'actionClassName' => 'Ice:Data_Model'
        ];
    }
}