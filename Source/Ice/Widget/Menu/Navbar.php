<?php

namespace Ice\Widget\Menu;

use Ice\Core\Route;
use Ice\Core\Widget;
use Ice\Core\Widget_Menu;
use Ice\Helper\Emmet;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\View\Render\Php;

class Navbar extends Widget_Menu
{
    /**
     * @var string
     */
    protected $brand = null;

    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    /**
     * @param $url
     * @param $action
     * @param null $block
     * @param array $data
     * @return Navbar
     */
    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data);
    }

    public function render()
    {
        /** @var Navbar $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();
        $widgetBaseClass = Object::getBaseClass($widgetClass, Widget::getClass());
        $widgetBaseClassName = $widgetBaseClass::getClassName();

        $parts = [];

        foreach ($this->getParts() as $partName => $part) {
            $part['widgetClassName'] = $widgetClassName;
            $part['widgetBaseClassName'] = $widgetBaseClassName;
            $part['token'] = $this->getToken();

            $part['name'] = isset($part['options']['name']) ? $part['options']['name'] : $partName;
            $part['value'] = isset($part['options']['value']);

            if (isset($part['options']['route'])) {
                if (is_array($part['options']['route'])) {
                    list($routeName, $params) = each($part['options']['route']);
                    $part['options']['href'] = Route::getInstance($routeName)->getUrl((array) $params);
                } else {
                    $part['options']['href'] = Route::getInstance($part['options']['route'])->getUrl();
                }
            }

            $position = isset($part['options']['position'])
                ? $part['options']['position']
                : '';

            $template = $part['template'][0] == '_'
                ? $widgetClass . $part['template']
                : $widgetBaseClass . '_' . $part['template'];

            $parts[$position][$partName] = Php::getInstance()->fetch($template, $part);
        }

        $widgetContent = Php::getInstance()->fetch(
            $widgetClass,
            [
                'parts' => $parts,
                'widgetData' => $this->getData(),
                'widgetClass' => $widgetClass,
                'widgetClassName' => $widgetClassName,
                'widgetBaseClassName' => $widgetBaseClassName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle(),
                'url' => $this->getUrl(),
                'token' => $this->getToken(),
                'dataJson' => Json::encode($this->getParams()),
                'dataAction' => $this->getAction(),
                'dataBlock' => $this->getBlock(),
                'dataUrl' => $this->getUrl() . '?' . http_build_query($this->getParams()),
            ]
        );

        return $this->getLayout()
            ? Emmet::translate($this->getLayout() . '{{$widgetContent}}', ['widgetContent' => $widgetContent])
            : $widgetContent;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     * @return Navbar
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }
}
