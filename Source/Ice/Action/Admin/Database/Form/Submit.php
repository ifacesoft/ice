<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 16.12.15
 * Time: 21:16
 */

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;

class Admin_Database_Form_Submit  extends Widget_Event
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'access' => ['roles' => 'ROLE_ICE_ADMIN', 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'widget' => ['default' => null, 'providers' => 'request'],
                'widgets' => ['default' => [], 'providers' => ['default', 'request']],
                'modelCLass' => ['providers' => 'request']
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $values = $input['widget']->getValues();

        Debuger::dump($input);

        return [
            'success' => $this->getLogger()->info('Запись сохранена', Logger::SUCCESS),
            'widgets' => parent::run($input)['widgets']
        ];
    }
}