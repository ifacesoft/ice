<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlResourceTag;

abstract class Resource extends Widget
{
    /**
     * Build link part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function link($columnName, array $options = [], $template = 'Ice\Widget\Resource\Link')
    {
        return $this->addPart(new HtmlResourceTag($columnName, $options, $template, $this));
    }

    /**
     * Build script part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function script($columnName, array $options = [], $template = 'Ice\Widget\Resource\Script')
    {
        return $this->addPart(new HtmlResourceTag($columnName, $options, $template, $this));
    }
}