<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Model;
use Ice\Helper\Emmet;

/**
 * Class Roll_Model
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.1
 * @since 0.1
 */
class Roll_Model extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Php',
        'inputValidators' => [
            'modelName' => 'Ice:Not_Empty'
        ],
        'layout' => Emmet::PANEL_BODY,
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
        $modelClass = Model::getClass($input['modelName']);

        return [
            'rows' => $modelClass::getQueryBuilder()
                ->select('*')
                ->desc('/pk')
                ->getQuery()
                ->getData()
        ];
    }
}