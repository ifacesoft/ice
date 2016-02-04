<?php
namespace Ice\Widget;

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
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
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
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    public function li($columnName, array $options = [], $template = 'Ice\Widget\Breadcrumbs\Li')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
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