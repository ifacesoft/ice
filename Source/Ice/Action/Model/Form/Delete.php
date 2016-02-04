<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Request;
use Ice\Widget\Form;

/**
 * Class Model_Delete
 *
 * Action for delete options
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 */
class Model_Form_Delete extends Widget_Event
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'widget' => ['providers' => 'request', 'validators' => 'Ice:Not_Empty'],
                'widgets' => ['default' => [], 'providers' => ['default', Request::class]]
            ],
            'output' => []
        ];
    }

    protected function initInput(array $configInput, array $data = [])
    {
        parent::initInput($configInput, $data);

        $input = $this->getInput();

        /** @var Form $modelFormWidget */
        $modelFormWidget = $input['widget'];

        /** @var Model $modelClass */
        $modelClass = $modelFormWidget->getInstanceKey();

        $modelFormWidget->bind(array_intersect_key(Request::getParams(), $modelClass::getScheme()->getFieldColumnMap()));

        $input['model'] = $modelClass::create($modelFormWidget->validate());

        $this->setInput($input);
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public function run(array $input)
    {
        $logger = Logger::getInstance(__CLASS__);

        try {
            /** @var Model $model */
            $model = $input['model'];

            $model->remove();

            return array_merge(
                parent::run($input),
                ['success' => $logger->info(['Model {$0} successfully removed', get_class($input['model'])], Logger::SUCCESS)]
            );
        } catch (\Exception $e) {
            $message = ['Remove model: {$0}', $e->getMessage()];

            $logger->error($message, __FILE__, __LINE__, $e);

            return ['error' => $logger->info($message, Logger::DANGER)];
        }
    }
}
