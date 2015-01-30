<?php
/**
 * Ice action model defined sync class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Data_Scheme;
use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\Model_Collection;
use Ice\Core\Model_Defined;

/**
 * Class Model_Defined_Sync
 *
 * Synchronize defined models
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since 0.0
 */
class Model_Defined_Sync extends Action
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

    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Model[] $modelClasses */
        $modelClasses = array_keys(Data_Scheme::getInstance()->getModelClasses());

        foreach ($modelClasses as $modelClass) {
            $modelClass = Model::getClass($modelClass);
            if (isset(class_parents($modelClass)[Model_Defined::getClass()])) {
                /** @var Model_Collection $rowCollection */
                $rowCollection = $modelClass::getCollection('*');

                $dataRows = $modelClass::getCollection('*')->getRows();

                if (!count($dataRows)) {
                    throw new Exception('Не определен конфиг Defined модели "' . $modelClass . '"');
                }

                foreach ($dataRows as $pk => $row) {
                    $model = $rowCollection->get($pk);
                    if ($model) {
                        $rowCollection->remove($pk)->save();
                        continue;
                    }
                    $modelClass::create($row)->save($row);
                }

                $rowCollection->remove();
            }
        }
    }
}