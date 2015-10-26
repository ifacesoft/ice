<?php
/**
 * Ice action resources class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Helper\Directory;
use Ice\Helper\File;
use JSMin;

/**
 * Class Title
 *
 * Action of generation js for includes into html tag head (<script.. and <link..)
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since   0.0
 */
class Resource_Js extends Action
{
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'input' => [
                'resources' => ['default' => []],
                'context' => ['default' => '/resource/'],
            ],
            'cache' => ['ttl' => 3600, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function run(array $input)
    {
        $resources = [];

        $compiledResourceDir = Module::getInstance()->get('compiledResourceDir');

        foreach (array_keys(Module::getAll()) as $name) {
            if (file_exists($jsSource = Module::getInstance($name)->get('path') . 'Resource/js/javascript.js')) {
                $resources[] = [
                    'source' => $jsSource,
                    'resource' => $compiledResourceDir . $name . '/javascript.pack.js',
                    'url' => $input['context'] . $name . '/javascript.pack.js',
                    'pack' => true
                ];
            }
        }

        foreach ($input['resources'] as $from => $config) {
            foreach ($config as $name => $configResources) {
                foreach ($configResources as $resourceKey => $resourceItem) {
                    $source = $from == 'modules' // else from vendors
                        ? Module::getInstance($name)->get('path') . 'Resource/'
                        : VENDOR_DIR . $name . '/';

                    $res = $from == 'modules' // else from vendors
                        ? $name . '/' . $resourceKey . '/'
                        : 'vendor/' . $resourceKey . '/';

                    $resourceItemPath = is_array($resourceItem['path'])
                        ? reset($resourceItem['path'])
                        : $resourceItem['path'];

                    $source .= $resourceItemPath;

                    if ($resourceItem['isCopy']) {
                        Directory::copy($source, Directory::get($compiledResourceDir . $res));
                    }

                    $jsResource = $compiledResourceDir . $res . $resourceKey . '.pack.js';

                    foreach ($resourceItem['js'] as $resource) {
                        $resources[] = [
                            'source' => $source . ltrim($resource, '-'),
                            'resource' => $jsResource,
                            'url' => $input['context'] . $res . $resourceKey . '.pack.js',
                            'pack' => $resource[0] != '-'
                        ];
                    }
                }
            }
        }

        return $this->pack($resources);
    }

    /**
     * Pack all resources in groupped files
     *
     * @param $resources
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function pack($resources)
    {
        if (!class_exists('JSMin', false) && !function_exists('jsmin')) {
            include_once VENDOR_DIR . 'mrclay/minify/min/lib/JSMin.php';

            /**
             * Custom implementation jsmin
             *
             * @param  $js
             * @return string
             */
            function jsmin($js)
            {
                return JSMin::minify($js);
            }
        }

        $cache = [];

        foreach ($resources as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                Directory::get(dirname($resource['resource']));
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? jsmin(file_get_contents($resource['source']))
                : file_get_contents($resource['source']);

            fwrite(
                $handlers[$resource['resource']],
                '/* ' . str_replace(dirname(MODULE_DIR), '', $resource['source']) . " */\n" . $pack . "\n\n\n"
            );

            if (!isset($cache[$resource['url']])) {
                $cache[$resource['url']] = [];
            }

            $cache[$resource['url']][] = $resource['source'];
        }

        foreach ($handlers as $filePath => $handler) {
            fclose($handler);

            if (function_exists('posix_getuid') && posix_getuid() == fileowner($filePath)) {
                chmod($filePath, 0666);
                chgrp($filePath, filegroup(dirname($filePath)));
            }
        }

        return [
            'javascripts' =>
                File::createData(Module::getInstance()->get(Module::COMPILED_RESOURCE_DIR) . 'javascript.cache.php', $cache)
        ];
    }
}
