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
use Ice\Core\DataProvider;
use Ice\Core\Loader;
use Ice\Core\Module;
use Ice\Core\Route;
use Ice\DataProvider\Router;
use Ice\Helper\Directory;
use Ice\Helper\File;
use JSMin\JSMin;
use Minify_CSSmin;

/**
 * Class Title
 *
 * Action of generation js and css for includes into html tag head (<script.. and <link..)
 *
 * @see \Ice\Core\Action
 * @see \Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since   0.0
 */
class Resource_Dynamic extends Action
{
    /**
     * Runtime append js resource
     *
     * @param $resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function appendJs($resource)
    {
        self::append('js', $resource);
    }

    /**
     * Runtime append resource
     *
     * @param $resourceType
     * @param $resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private static function append($resourceType, $resource)
    {
        /**
         * @var Action $actionClass
         */
        $actionClass = self::getClass();

        $dataProvider = DataProvider::getInstance($actionClass::getRegistryDataProviderKey());

        $customResources = $dataProvider->get($resourceType);

        if (!$customResources) {
            $customResources = [];
        }

        array_push($customResources, $resource);

        $dataProvider->set($resourceType, $customResources);
    }

    /**
     * Runtime append css resource
     *
     * @param $resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function appendCss($resource)
    {
        self::append('css', $resource);
    }

    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'input' => [
                'js' => ['default' => []],
                'css' => ['default' => []],
                'routeName' => ['providers' => ['default', Router::class], 'default' => '/'],
                'context' => ['default' => '/resource/'],
                'widgetClasses' => ['default' => ['js' => [], 'css' => [], 'less' => []]],
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
        $resources = [
            'js' => [],
            'css' => []
        ];

        $compiledResourceDir = getCompiledResourceDir();

        $moduleAlias = Module::getInstance()->getAlias();

        $jsRes = $moduleAlias . '/js/';
        $cssRes = $moduleAlias . '/css/';

        $resourceName = Route::getInstance($input['routeName'])->getName();

        $jsFile = $resourceName . '.pack.js';
        $cssFile = $resourceName . '.pack.css';

        $jsResource = Directory::get($compiledResourceDir . $jsRes) . $jsFile;
        $cssResource = Directory::get($compiledResourceDir . $cssRes) . $cssFile;

        foreach ($input['widgetClasses']['js'] as $resourceFile) {
            if (file_exists($jsSource = Loader::getFilePath($resourceFile, '', Module::RESOURCE_DIR))) {
                $resources['js'][] = [
                    'source' => $jsSource,
                    'resource' => $jsResource,
                    'url' => $input['context'] . $jsRes . $jsFile,
                    'pack' => true
                ];
            }
        }

        foreach ($input['widgetClasses']['css'] as $resourceFile) {
            if (file_exists($cssSource = Loader::getFilePath($resourceFile, '', Module::RESOURCE_DIR))) {
                $resources['css'][] = [
                    'source' => $cssSource,
                    'resource' => $cssResource,
                    'url' => $input['context'] . $cssRes . $cssFile,
                    'pack' => true,
                    'css_replace' => []
                ];
            }
        }

        $jsFile = 'custom.pack.js';
        $cssFile = 'custom.pack.css';

        $jsResource = Directory::get($compiledResourceDir . $jsRes) . $jsFile;
        $cssResource = Directory::get($compiledResourceDir . $cssRes) . $cssFile;

        if (!empty($input['js'])) {
            foreach ($input['js'] as $resource) {
                $resources['js'][] =
                    [
                        'source' => Loader::getFilePath($resource, '.js', 'Resource/js/'),
                        'resource' => $jsResource,
                        'url' => $input['context'] . $jsRes . $jsFile,
                        'pack' => true
                    ];
            }
        }
        if (!empty($input['css'])) {
            foreach ($input['css'] as $resource) {
                $resources['css'][] =
                    [
                        'source' => Loader::getFilePath($resource, '.css', 'Resource/css/'),
                        'resource' => $cssResource,
                        'url' => $input['context'] . $cssRes . $cssFile,
                        'pack' => true,
                        'css_replace' => []
                    ];
            }
        }

        return $this->pack($resources, $input['routeName']);
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
    private function pack($resources, $routeName)
    {
        $handlers = [];

        $CSSmin = new Minify_CSSmin();

        $cache = [
            'js' => [],
            'css' => [],
            'less' => []
        ];

        foreach ($resources['js'] as $resource) {
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

            if (!isset($cache['js'][$resource['url']])) {
                $cache['js'][$resource['url']] = [];
            }

            $cache['js'][$resource['url']][] = $resource['source'];
        }

        foreach ($resources['css'] as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                Directory::get(dirname($resource['resource']));
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? $CSSmin->minify(file_get_contents($resource['source']))
                : file_get_contents($resource['source']);

            if (!empty($resource['css_replace'])) {
                $pack = str_replace($resource['css_replace'][0], $resource['css_replace'][1], $pack);
            }

            fwrite($handlers[$resource['resource']], '/* Ice: ' . $resource['source'] . " */\n" . $pack . "\n\n\n");

            if (!isset($cache['css'][$resource['url']])) {
                $cache['css'][$resource['url']] = [];
            }

            $cache['css'][$resource['url']][] = $resource['source'];
        }

        foreach ($handlers as $filePath => $handler) {
            fclose($handler);

            if (function_exists('posix_getuid') && posix_getuid() == fileowner($filePath)) {
                chmod($filePath, 0666);
                chgrp($filePath, filegroup(dirname($filePath)));
            }
        }

        $resourceDir = getCompiledResourceDir();

        return [
            'javascripts' => File::createData($resourceDir . 'javascript.' . $routeName . '.cache.php', $cache['js']),
            'styles' => File::createData($resourceDir . 'style.' . $routeName . '.cache.php', $cache['css']),
        ];
    }
}
