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
        $modelClass = Model::getClass($input['modelClassName']);

        $actionContext->addAction(
            'Ice:Form_Model',
            [
                'modelClassName' => $input['modelClassName'],
                'pk' => 0,
                'filterFields' => $input['formFilterFields'],
                'groupping' => 0,
                'submitTitle' => Data_Model::getResource()->get('Add') . ' ' . $modelClass::getTitle(),
                'template' => '_Modal',
                'params' => [
                    'modelClassName' => $input['modelClassName'],
                    'formFilterFields' => $input['formFilterFields'],
                    'dataFilterFields' => $input['dataFilterFields'],
                    'submitActionName' => $input['submitActionName'],
                    'reRenderClosest' => $input['reRenderClosest'],
                    'reRenderActionNames' => $input['reRenderActionNames'],
                ]
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
                    'modelClassName' => $input['modelClassName'],
                    'formFilterFields' => $input['formFilterFields'],
                    'dataFilterFields' => $input['dataFilterFields'],
                    'submitActionName' => $input['submitActionName'],
                    'reRenderClosest' => $input['reRenderClosest'],
                    'reRenderActionNames' => $input['reRenderActionNames'],
                ]
            ]
        );

        $data = $modelClass::getData($input['dataFilterFields'])
            ->button('blog_pk', 'Изменить', [
                'onclick' => 'Ice_Form.modal($(this), \'Bi:Blog\', 1, \'Ice:Form_Submit\', 0, \'Add blog\', \'_Modal\', {reRenderClosest: \'Ice:Data_Model\', reRenderActionClassName: [], formFilterFields: [\'blog_name\'], dataFilterFields: [\'blog_pk\', \'blog_name\']}); return false;',
                'type' => 'info',
                'icon' => 'edit'
            ])
            ->button('blog_pk', 'Удалить', [
                'onclick' => 'Ice_Form.modal($(this), \'Bi:Blog\', 1, \'Ice:Form_Submit\', 0, \'Add blog\', \'_Modal\', {reRenderClosest: \'Ice:Data_Model\', reRenderActionClassName: [], formFilterFields: [\'blog_name\'], dataFilterFields: [\'blog_pk\', \'blog_name\']}); return false;',
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