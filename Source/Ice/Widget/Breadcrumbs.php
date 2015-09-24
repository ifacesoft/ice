<?php
namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

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
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    /**
     * Init widget parts and other
     * @param array $input
     * @return array|void
     */
    public function init(array $input)
    {
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
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    public function li($columnName, array $options = [], $template = 'Ice\Widget\Breadcrumbs\Li')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }
}