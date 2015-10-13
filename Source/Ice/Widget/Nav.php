<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

class Nav extends Widget
{
    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function li($name, array $options = [], $template = 'Ice\Widget\Nav\Li')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function dropdown($name, array $options = [], $template = 'Ice\Widget\Nav\Dropdown')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function nav($name, array $options = [], $template = 'Ice\Widget\Nav\Nav')
    {
        $options['widget']->setClasses($options['widget']->getClasses() . ' nav-nav');

        return $this->widget($name, $options, $template);
    }

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'url' => true,
                //  'method' => 'POST',
                //  'callback' => null
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
        return [];
    }
}