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
use Ice\Exception\Error;
use Ice\Helper\Directory;
use Ice\Helper\File;
use JSMin\JSMin;

/**
 * Class Title
 *
 * Action of generation js for includes into html tag head (<script.. and <link..)
 *
 * @see \Ice\Core\Action
 * @see \Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
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
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     * @throws \Ice\Core\Exception
     */
    public function run(array $input)
    {
        $resources = [];

        $compiledResourceDir = getCompiledResourceDir();

        foreach (array_keys(Module::getAll()) as $moduleName) {
            if (file_exists($jsSource = getResourceDir($moduleName) . 'js/javascript.js')) {
                $resources[] = [
                    'source' => $jsSource,
                    'resource' => $compiledResourceDir . $moduleName . '/javascript.pack.js',
                    'url' => $input['context'] . $moduleName . '/javascript.pack.js',
                    'pack' => true
                ];
            }
        }

        foreach ($input['resources'] as $from => $config) {
            foreach ($config as $moduleName => $configResources) {
                foreach ($configResources as $resourceKey => $resourceItem) {
                    switch ($from) {
                        case 'modules':
                            $source = getResourceDir($moduleName);
                            $res = $moduleName . '/' . $resourceKey . '/';
                            break;
                        case 'node_modules';
                            $source = NODE_MODULES_DIR . $resourceKey . '/';
                            $res = 'node_modules/' . $resourceKey . '/';
                            break;
                        default:
                            throw new Error(['From {$0} handler not implemented', $from]);
                    }

                    $resourceItemPath = is_array($resourceItem['path'])
                        ? reset($resourceItem['path'])
                        : $resourceItem['path'];

                    $source .= $resourceItemPath;

                    if (isset($resourceItem['isCopy']) && $resourceItem['isCopy'] === true) {
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
     * @return array
     * @throws \Ice\Core\Exception
     */
    private function pack($resources)
    {
        $handlers = [];

        $cache = [];

        foreach ($resources as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                Directory::get(dirname($resource['resource']));
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? JSMin::minify(file_get_contents($resource['source']))
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
                File::createData(getCompiledResourceDir() . 'javascript.cache.php', $cache)
        ];
    }
}
