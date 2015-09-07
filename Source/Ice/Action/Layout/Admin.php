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
                'actions' => [
                    'Ice:Admin_Navigation',
                    'Ice:Admin_Menu',
                    'Ice:Resource_Css',
                    'Ice:Resource_Js',
                    'Ice:Resource_Dynamic'
                ],
                'access' => ['roles' => ['ROLE_ICE_ADMIN']]

            ],
            parent::config()
        );
    }
}
