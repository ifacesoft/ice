<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlTag;

class Table extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    public function getPagination($widgetClass = Pagination::class)
    {
        return $this->getWidget($widgetClass);
    }

    public function row($fieldName, array $options = [], $template = 'Ice\Widget\Table_Row')
    {
        return $this->addPart(new HtmlTag($fieldName, $options, $template, $this));
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