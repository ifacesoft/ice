<?php
namespace Ice\Core;

use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;

abstract class View extends Container
{
    use Stored;
    use Rendered;

    /**
     * @var string
     */
    private $template = null;

    /**
     * @var string
     */
    private $renderClass = null;

    /**
     * @var string
     */
    private $layout = null;

    private $dataParams = null;

    /**
     * @var string
     */
    private $actionClass = null;

    /**
     * @var string
     */
    private $viewClass = null;

    /**
     * @var string
     */
    private $dataUrl = null;

    /**
     * @var string
     */
    private $result = null;

    /**
     * @var Widget[]
     */
    private $widgets = [];

    /**
     * @var View[]
     */
    private $observers = [];

    /**
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return View
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * @param View $view
     * @param $viewClass
     * @return $this
     */
    public function addObserver(View $view, $viewClass) {
        $this->observers[$view->getForViewId()] = $viewClass;

        return $this;
    }

    /**
     * @param null $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param null $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param null $renderClass
     */
    public function setRenderClass($renderClass)
    {
        $this->renderClass = $renderClass;
    }

    /**
     * @return string
     */
    public function getViewClass()
    {
        if ($this->viewClass !== null) {
            return $this->viewClass;
        }

        return $this->viewClass = get_class($this);
    }

    /**
     * @return string
     */
    public function getActionClass()
    {
        if ($this->actionClass !== null) {
            return $this->actionClass;
        }

        return $this->viewClass = Render::getClass();
    }

    /**
     * @return string
     */
    public function getDataUrl()
    {
        if ($this->dataUrl !== null) {
            return $this->dataUrl;
        }

        return $this->dataUrl = Request::uri(true);
    }

    protected function init(array $params) {
        $this->dataParams = Input::get(self::getClass());

        $this->build($this->dataParams);
    }

    protected abstract function build($input);

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage() . ' - ' . $this->getClass() . ' (' . $e->getFile() . ':' . $e->getLine() . ')<br>' .
            nl2br($e->getTraceAsString());
        }
    }

    public function render()
    {
        if ($this->result !== null) {
            return $this->result;
        }

        $this->result = '';

        $output = (array) $this->build($this->dataParams);

        $dataParams = $this->dataParams;

        foreach ($this->widgets as $widget) {
            $dataParams = array_merge($widget->getDataParams(), $dataParams);
        }

        /** @var View $viewClass */
        $viewClass = get_class($this);

        $template = $viewClass::getTemplate($this->template);

        if (!$template) {
            foreach ($this->widgets as $widget) {
                $widget->setDataParams($dataParams);
                $this->result .= $widget->render();
            }
        } else {
            $data = [];

            foreach ($this->widgets as $widget) {
                $widget->setDataParams($dataParams);
                $data[$widget->getInstanceKey()] = $widget->render();
            }

            $this->result = $viewClass::getRender($this->renderClass)
                ->fetch($template, array_merge($output, $data));
        }

        $layout = $this->getLayout(
            $this->layout,
            '[data-action="' . $this->getActionClass() .
            '" data-view="' . $this->getViewClass() .
            '" data-params="' . Json::encode($dataParams) .
            '"]'
        );
//
//        require_once VENDOR_DIR . 'ifacesoft/ice/Source/artem_c/emmet/Emmet.php';
//        require_once VENDOR_DIR . 'ifacesoft/ice/Source/Ice/Helper/Emmet.php';
//
//        return $layout
//            ? $emmet->create(['viewContent' => $this->result])
//            : $this->result;
//
//        return $layout
//            ? Emmet::translate($layout . '{{$viewContent}}', ['viewContent' => $this->result])
//            : $this->result;

        return $layout
            ? '<div id="' . $this->getForViewId() . '" class="View"' .
//            ' data-action="' . $this->getActionClass() . '"' .
//            ' data-view="' . $this->getViewClass() . '"' .
            ' data-params=\'' . Json::encode($dataParams) . '\'' .
            ' data-observers=\'' . Json::encode($this->observers) . '\'' .
            '>' . $this->result . '</div>'
            : $this->result;
    }

    /**
     * @param $widgetClass
     * @param $name
     * @param array $params
     * @return Widget
     */
    protected function getWidget($widgetClass, $name, array $params = [])
    {
        $params = array_merge(['forViewId' => $this->getForViewId(), 'resource' => get_class($this)], $params);

        /** @var Widget $widgetClass */
        $widgetClass = Widget::getClass($widgetClass);

        $widget = $widgetClass::getInstance($name, null,  $params);

        $this->widgets[$name] = $widget;

        return $widget;
    }

    private function getForViewId() {
        return 'View_' . Object::getClassName(get_class($this)) . '_' . $this->getInstanceKey();
    }

    protected function getWidgetClass($widgetClass)
    {
        if ($widgetClass[0] == '_') {
            /** @var View viewClass */
            $viewClass = get_class($this);
            $widgetClass = $viewClass::getModuleAlias() . ':' . $viewClass::getClassName() . $widgetClass;
        }

        return Widget::getClass($widgetClass);
    }
}