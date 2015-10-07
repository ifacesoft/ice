<?php

namespace Ice\Widget;

class Admin_Sidebar extends Nav
{

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Nav::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'routeNames' => ['default' => []],
                'routeName' => ['providers' => 'router']
            ],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'method' => 'POST'
            ]
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $routeName = $this->getRouteName($input['routeNames'], $input['routeName']);

        if (!$routeName) {
            $routeName = $this->getRouteName($input['routeNames'], substr($input['routeName'], 0, strrpos($input['routeName'], '_')));
        }

        $this->widget('sidebar', ['widget' => $this->getNav($input['routeNames'][$routeName])]);
    }

    private function getRouteName(array $routeNames, $currentRouteName)
    {
        foreach ($routeNames as $key => $routeName) {
            if ($routeName == $currentRouteName) {
                return $key;
            }

            if (is_array($routeName) && $this->getRouteName($routeName, $currentRouteName) !== null) {
                return $key;
            }
        }

        return null;
    }

    private function getNav(array $routeNames, $routeName = null)
    {
        /** @var NAv $nav */
        $nav = Admin_Nav::getInstance('sidebar_' . $routeName);

        if ($routeName) {
            $nav->widget(
                $routeName . '_main',
                ['widget' => Header::getInstance($routeName . '_header')->h4($routeName, ['route' => true])]
            );
        }

        foreach ($routeNames as $key => $routeName) {
            if (is_array($routeName)) {
                $nav->widget($key . '_nav', ['widget' => $this->getNav($routeName, $key)]);
            } else {
                $nav->li($routeName, ['route' => true]);
            }
        }

        return $nav;
    }
}