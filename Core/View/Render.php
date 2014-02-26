<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 10.01.14
 * Time: 23:09
 */

namespace ice\core;


use ice\Ice;

abstract class View_Render
{
    public static $config = array();

    private $_config = null;

    private function __construct()
    {
    }

    abstract public function init();

    abstract public function display($template, array $data = array(), $ext);

    abstract public function fetch($template, array $data = array(), $ext);

    /**
     * @return View_Render
     */
    public static function get()
    {
        /** @var View_Render $viewRenderClass */
        $viewRenderClass = get_called_class();

        /** @var Data_Provider $dataProvider */
        $dataProvider = Data_Provider::getInstance(Ice::getConfig()->getParam('viewRenderDataProviderKey'));

        $viewRender = $dataProvider->get($viewRenderClass); //$viewRender = null;

        if ($viewRender) {
            return $viewRender;
        }

        /** @var View_Render $viewRender */
        $viewRender = new $viewRenderClass();
        $viewRender->init();

        $dataProvider->set($viewRenderClass, $viewRender);

        return $viewRender;
    }

    public function getConfig()
    {
        if ($this->_config !== null) {
            return $this->_config;
        }

        $this->_config = Config::get($this->getClass(), self::$config);
        return $this->_config;
    }

    public function getClass()
    {
        return get_class($this);
    }
}