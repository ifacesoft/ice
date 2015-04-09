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
                'filterForm' => ['validators' => 'Ice:Is_Ui_Form'],
                'tableData' => ['validators' => 'Ice:Is_Ui_Data'],
                'paginationMenu' => ['validators' => 'Ice:Is_Ui_Menu'],
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