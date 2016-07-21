<?php
namespace Ice\Widget;

use Ice\Action\Resource_Css;
use Ice\Action\Resource_Js;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Module;
use Ice\Helper\File;
use Ice\Helper\Hash;
use Ice\Helper\Serializer;

class Resource_Static extends Resource
{
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
        $styleCacheFile = getCompiledResourceDir() . 'style.cache.php';

        $styles = File::loadData($styleCacheFile, false);

        if ($styles === null) {
            $styles = Resource_Css::call()['styles'];
        } else {
            if (!Environment::getInstance()->isProduction()) {
                $cacheFiletime = filemtime($styleCacheFile);

                foreach ($styles as $css => $sources) {
                    foreach ($sources as $source) {
                        if (!file_exists($source) || filemtime($source) > $cacheFiletime) {
                            $styles = Resource_Css::call()['styles'];
                            break 2;
                        }
                    }
                }
            }
        }

        foreach ($styles as $css => $sources) {
            $this->link(Hash::get($sources, Hash::HASH_CRC32) . '_css', ['value' => $css]);
        }

        $javascriptCacheFile = getCompiledResourceDir() . 'javascript.cache.php';

        $javascripts = File::loadData($javascriptCacheFile, false);

        if ($javascripts === null) {
            $javascripts = Resource_Js::call()['javascripts'];
        } else {
            if (!Environment::getInstance()->isProduction()) {
                $cacheFiletime = filemtime($javascriptCacheFile);

                foreach ($javascripts as $js => $sources) {
                    foreach ($sources as $source) {
                        if (!file_exists($source) || filemtime($source) > $cacheFiletime) {
                            $javascripts = Resource_Js::call()['javascripts'];
                            break 2;
                        }
                    }
                }
            }
        }

        foreach ($javascripts as $js => $sources) {
            $this->script(Hash::get($sources, Hash::HASH_CRC32) . '_js', ['value' => $js]);
        }
    }
}