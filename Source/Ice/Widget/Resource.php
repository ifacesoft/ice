<?php

namespace Ice\Widget;

use Ice\Core\Widget;

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
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
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
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }
}