<?php

namespace Ice\Widget;

use Ice\Action\Resource_Dynamic as Action_Resource_Dynamic;
use Ice\Core\Environment;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\DataProvider\Router;
use Ice\Helper\File;

class Resource_Dynamic extends Resource
{
    private $loaded = false;
    private $widgetClasses = [
        'js' => [],
        'css' => [],
        'less' => []
    ];

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => '', 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['routeName' => ['providers' => Router::class, 'default' => '/'],],
            'output' => [],
        ];
    }

    public function isLoaded()
    {
        return $this->loaded;
    }

    public function addResource($widgetClass, $type)
    {
        $this->widgetClasses[$type][] = $widgetClass;
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $javascriptCacheFile = Module::getInstance()->get(Module::COMPILED_RESOURCE_DIR) . 'javascript.' . $input['routeName'] . '.cache.php';

        $javascripts = File::loadData($javascriptCacheFile, false);

        if ($javascripts === null) {
            return;
        } else {
            if (!Environment::getInstance()->isProduction()) {
                $cacheFiletime = filemtime($javascriptCacheFile);

                foreach ($javascripts as $css => $sources) {
                    foreach ($sources as $source) {
                        if (!file_exists($source) || filemtime($source) > $cacheFiletime) {
                            return;
                        }
                    }
                }
            }
        }

        foreach ($javascripts as $js => $sources) {
            $this->script($js, ['resource' => false]);
        }

        $styleCacheFile = Module::getInstance()->get(Module::COMPILED_RESOURCE_DIR) . 'style.' . $input['routeName'] . '.cache.php';

        $styles = File::loadData($styleCacheFile, false);

        if ($styles === null) {
            return;
        } else {
            if (!Environment::getInstance()->isProduction()) {
                $cacheFiletime = filemtime($styleCacheFile);

                foreach ($styles as $css => $sources) {
                    foreach ($sources as $source) {
                        if (!file_exists($source) || filemtime($source) > $cacheFiletime) {
                            return;
                        }
                    }
                }
            }
        }

        foreach ($styles as $css => $sources) {
            $this->link($css, ['resource' => false]);
        }

        $this->loaded = true;
    }

    public function render(Render $render = null)
    {
        if (!$this->loaded) {
            $this->loaded = true;

            Action_Resource_Dynamic::call(['widgetClasses' => $this->widgetClasses]);
            $this->build($this->getValues());
        }

        return parent::render();
    }
}