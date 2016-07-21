<?php
/**
 * Ice action resources class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use CSSmin;
use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Helper\Directory;
use Ice\Helper\File;

/**
 * Class Title
 *
 * Action of generation css for includes into html tag head (<script.. and <link..)
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 */
class Resource_Css extends Action
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
        $compiledResourceDir = getCompiledResourceDir();

        foreach (array_keys(Module::getAll()) as $moduleName) {
            if (file_exists($cssSource = getResourceDir($moduleName) . 'css/style.css')) {
                $resources[] = [
                    'source' => $cssSource,
                    'resource' => $compiledResourceDir . $moduleName . '/style.pack.css',
                    'url' => $input['context'] . $moduleName . '/style.pack.css',
                    'pack' => true,
                    'css_replace' => []
                ];
            }
        }

        foreach ($input['resources'] as $from => $config) {
            foreach ($config as $moduleName => $configResources) {
                foreach ($configResources as $resourceKey => $resourceItem) {
                    $source = $from == 'modules' // else from vendors
                        ? getResourceDir($moduleName)
                        : VENDOR_DIR . $moduleName . '/';

                    $res = $from == 'modules' // else from vendors
                        ? $moduleName . '/' . $resourceKey . '/'
                        : 'vendor/' . $resourceKey . '/';

                    $resourceItemPath = is_array($resourceItem['path'])
                        ? reset($resourceItem['path'])
                        : $resourceItem['path'];

                    $source .= $resourceItemPath;

                    if ($resourceItem['isCopy']) {
                        Directory::copy($source, Directory::get($compiledResourceDir . $res));
                    }

                    foreach ($resourceItem['css'] as $resource) {
                        $resources[] = [
                            'source' => $source . ltrim($resource, '-'),
                            'resource' => $compiledResourceDir . $res . $resourceKey . '.pack.css',
                            'url' => $input['context'] . $res . $resourceKey . '.pack.css',
                            'pack' => $resource[0] != '-',
                            'css_replace' => isset($resourceItem['css_replace']) ? $resourceItem['css_replace'] : []
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
     * @return array
     */
    private function pack($resources)
    {
        if (!class_exists('CSSMin', false)) {
            include_once VENDOR_DIR . 'mrclay/minify/min/lib/CSSmin.php';
        }
        $handlers = [];

        $CSSmin = new CSSMin();

        $cache = [];

        foreach ($resources as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                Directory::get(dirname($resource['resource']));
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? $CSSmin->run(file_get_contents($resource['source']))
                : file_get_contents($resource['source']);

            if (!empty($resource['css_replace'])) {
                $pack = str_replace($resource['css_replace'][0], $resource['css_replace'][1], $pack);
            }

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
            'styles' =>
                File::createData(getCompiledResourceDir() . 'style.cache.php', $cache)
        ];
    }
}
