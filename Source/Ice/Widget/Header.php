<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

abstract class Header extends Widget
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
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h1($name, array $options = [], $template = 'Ice\Widget\Header\H1')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h2($name, array $options = [], $template = 'Ice\Widget\Header\H2')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h3($name, array $options = [], $template = 'Ice\Widget\Header\H3')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h4($name, array $options = [], $template = 'Ice\Widget\Header\H4')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h5($name, array $options = [], $template = 'Ice\Widget\Header\H5')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h6($name, array $options = [], $template = 'Ice\Widget\Header\H6')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }
}