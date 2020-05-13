<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlTag;

class Header extends Widget
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
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h1($name, array $options = [], $template = 'Ice\Widget\Header\H1')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h2($name, array $options = [], $template = 'Ice\Widget\Header\H2')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h2a($name, array $options = [], $template = 'Ice\Widget\Header\H2a')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h3($name, array $options = [], $template = 'Ice\Widget\Header\H3')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h3a($name, array $options = [], $template = 'Ice\Widget\Header\H3a')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h4($name, array $options = [], $template = 'Ice\Widget\Header\H4')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h4a($name, array $options = [], $template = 'Ice\Widget\Header\H4a')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h5($name, array $options = [], $template = 'Ice\Widget\Header\H5')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Header
     */
    public function h6($name, array $options = [], $template = 'Ice\Widget\Header\H6')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
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