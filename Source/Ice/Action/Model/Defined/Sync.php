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
use Ice\Core\Data_Scheme;
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
            'view' => ['template' => '']
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function run(array $input)
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
                    Model_Defined_Sync::getLogger()->exception(['Не определен конфиг Defined модели "{$0}"', $modelClass], __FILE__, __LINE__);
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