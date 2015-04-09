<?php

namespace Ice\Action;

use Ice\Core\Action;

class Report extends Action
{
    /**
     * Action config
     *
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
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'input' => [
                'name' => ['validators' => 'Ice:Not_Empty'],
                'desc',
                'filterForm',
                'tableData',
                'paginationMenu'
            ],
            'output' => [],
            'ttl' => -1,
            'roles' => []
        ];
    }

    /** Run action
     *
     * @param array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function run(array $input)
    {
        return $input;
    }
}