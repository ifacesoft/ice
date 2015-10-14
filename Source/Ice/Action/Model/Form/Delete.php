<?php

namespace Ice\Action;

use Ice\Core\Action;

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
                'widgets' => ['default' => [], 'providers' => ['default', 'request']]
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
//        return Query::getBuilder(Model::getClass($input['modelClassName']))
//            ->deleteQuery($input['pk'])
//            ->getQueryResult()
//            ->getAffectedRows()
//            ? ['success' => $this->getLogger()->info('Delete successfully', Logger::SUCCESS)]
//            : ['error' => $this->getLogger()->info('Delete failed', Logger::DANGER)];
    }
}
