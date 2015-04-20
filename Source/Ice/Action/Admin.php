<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Widget\Data\Table;

class Admin extends Action
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
            'input' => [
                'items' => ['default' => [], 'type' => 'array']
            ],
            'output' => [],
            'ttl' => -1,
            'roles' => []
        ];
    }

    /**
     * Run action
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
        $input['items'][] = [
            'title' => 'База данных',
            'desc' => 'Менеджер базы данных.',
            'routeName' => 'ice_admin_database'
        ];

        $data = Table::create()
            ->setColumnCount(5)
            ->link('title', 'item', ['routeName' => 'routeName'])
            ->setRows($input['items']);

        return [
            'table' => $data
        ];
    }
}
