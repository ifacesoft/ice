<?php

namespace Ice\Action;

use Ice\Core\Action;

class Orm_Migrate extends Action
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
            'view' => ['template' => ''],
            'actions' => [
                'Ice:Orm_Sync_DataScheme',
                'Ice:Orm_Sync_DataSource'
            ],
            'input' => ['force' => ['default' => 0]],
            'output' => [],
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
    }
}