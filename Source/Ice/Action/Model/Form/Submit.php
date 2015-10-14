<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Request;
use Ice\Core\Widget;
use Ice\Widget\Form;

class Model_Form_Submit extends Widget_Event
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
                'widget' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
            ],
            'output' => []
        ];
    }

    protected function init(array $data = [])
    {
        parent::init($data);

        $input = $this->getInput();

        /** @var Form $modelFormWidget */
        $modelFormWidget = $input['widget'];
        unset($input['widget']);

        /** @var Model $modelClass */
        $modelClass = $modelFormWidget->getInstanceKey();

        $modelFormWidget->bind(Request::getParams($modelClass::getScheme()->getColumnFieldMap()));

        $this->setInput(array_merge($input, $modelFormWidget->validate()));
    }


    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        Debuger::dump($input);die();
    }
}
