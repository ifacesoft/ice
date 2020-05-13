<?php
namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlTag;

class Breadcrumbs extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => [],
        ];
    }

    /**
     * Build a of breadcrumbs
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return Breadcrumbs
     */
    public function item($columnName, array $options = [], $template = 'Ice\Widget\Breadcrumbs\Item')
    {
        $options['excel'] = ['rowVisible' => false];

        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
    }

    public function li($columnName, array $options = [], $template = 'Ice\Widget\Breadcrumbs\Li')
    {
        $options['excel'] = ['rowVisible' => false];

        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
    }

    /**
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }
}