<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlTag;

class Nav extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Nav::class, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function li($name, array $options = [], $template = 'Ice\Widget\Nav\Li')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function dropdown($name, array $options = [], $template = 'Ice\Widget\Nav\Dropdown')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function nav($name, array $options = [], $template = 'Ice\Widget\Nav\Nav')
    {
        $options['widget']->addClasses('nav-nav');

        return $this->widget($name, $options, $template);
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