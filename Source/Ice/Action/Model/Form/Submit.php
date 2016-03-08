<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Request;
use Ice\DataProvider\Request as DataProvider_Request;
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
                'widget' => ['providers' => DataProvider_Request::class, 'validators' => 'Ice:Not_Empty'],
                'widgets' => ['default' => [], 'providers' => ['default', DataProvider_Request::class]]
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


    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $logger = Logger::getInstance(__CLASS__);

        try {
            /** @var Model $model */
            $model = $input['model'];

            $model->save();

            return array_merge(
                parent::run($input),
//                ['success' => $logger->info(['Model {$0} successfully saved', get_class($input['model'])], Logger::SUCCESS, true)]
                ['success' => $logger->info(['Сохранение прошло успешно', get_class($input['model'])], Logger::SUCCESS, true)]
            );
        } catch (\Exception $e) {
            $message = ['Save model: {$0}', $e->getMessage()];

            $logger->error($message, __FILE__, __LINE__, $e);

            return
//                ['error' => $logger->info($message, Logger::DANGER, true)]
                ['error' => $logger->info('Сохранение не удалось', Logger::DANGER, true)]
                ;
        }
    }
}
