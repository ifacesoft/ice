<?php
namespace Ice\Action;

class Layout_Admin extends Layout
{
    /**
     * @return array
     */
    protected static function config()
    {
        return array_merge_recursive(
            [
                'actions' => ['Ice:Resources', 'Ice:Admin_Navigation']
            ],
            parent::config()
        );
    }
}
