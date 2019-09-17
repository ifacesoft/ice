<?php

namespace Ice\Action;


use Ice\Core\Action;
use Ice\Helper\Api_Client_Yandex_Translate;

class Header extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'actions' => '_Menu',
            'input' => [],
            'output' => ['resource' => 'Ice:Resource/Ice\Action\Header'],
            'ttl' => -1,
            'roles' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        return ['flags' => Api_Client_Yandex_Translate::getFlags()];
    }
}