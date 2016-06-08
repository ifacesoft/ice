<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\Table_Row_A;
use Ice\WidgetComponent\Table_Row_Td;
use Ice\WidgetComponent\Table_Row_Th;

class Bootstrap3_Table_Row extends Widget
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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        // TODO: Implement build() method.
    }

    /**
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function a($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new Table_Row_A($columnName, $options, $template, $this));
    }

    /**
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function td($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new Table_Row_Td($columnName, $options, $template, $this));
    }

    /**
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function th($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new Table_Row_Th($columnName, $options, $template, $this));
    }
}