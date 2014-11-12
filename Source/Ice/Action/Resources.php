<?php
/**
 * Ice action resources class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use CSSmin;
use Ice;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Data_Provider;
use Ice\Core\Loader;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Data\Provider\Router;
use Ice\Helper\Arrays;
use Ice\Helper\Directory;
use JSMin;

/**
 * Class Title
 *
 * Action of generation js and css for includes into html tag head (<script.. and <link..)
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since 0.0
 */
class Resources extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'layout' => '',
        'cacheDataProviderKey' => 'Ice:File/cache',
        'viewRenderClassName' => 'Ice:Php',
        'inputDataProviderKeys' => Router::DEFAULT_KEY
    ];

    /**
     * Runtime append js resource
     *
     * @param $resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * @since 0.0
     */
    private static function append($resourceType, $resource)
    {
        /** @var Action $actionClass */
        $actionClass = self::getClass();

        $dataProvider = Data_Provider::getInstance($actionClass::getRegistryDataProviderKey());

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
     * @since 0.0
     */
    public static function appendCss($resource)
    {
        self::append('css', $resource);
    }

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $resources = [
            'js' => [],
            'css' => []
        ];

        foreach (Module::getAliases() as $name) {
            $modulePath = Module::getInstance($name)->getPath();
            $jsResource = RESOURCE_DIR . $name . '/javascript.pack.js';
            $cssResource = RESOURCE_DIR . $name . '/style.pack.css';

            if (file_exists($jsSource = $modulePath . 'Resource/js/javascript.js')) {
                $resources['js'][] = [
                    'source' => $jsSource,
                    'resource' => $jsResource,
                    'url' => '/resource/' . $name . '/javascript.pack.js',
                    'pack' => true
                ];
            }
            if (file_exists($cssSource = $modulePath . 'Resource/css/style.css')) {
                $resources['css'][] = [
                    'source' => $cssSource,
                    'resource' => $cssResource,
                    'url' => '/resource/' . $name . '/style.pack.css',
                    'pack' => true,
                    'css_replace' => []
                ];
            }


            if (file_exists($imgSource = $modulePath . 'Resource/img')) {
                Directory::copy($imgSource, Directory::get(RESOURCE_DIR . 'img'));
            }
            if (file_exists($apiSource = $modulePath . 'Resource/api')) {
                Directory::copy($apiSource, Directory::get(RESOURCE_DIR . 'api'));
            }
            if (file_exists($umlSource = $modulePath . 'Resource/uml')) {
                Directory::copy($umlSource, Directory::get(RESOURCE_DIR . 'uml'));
            }
            if (file_exists($docSource = $modulePath . 'Resource/doc')) {
                Directory::copy($docSource, Directory::get(RESOURCE_DIR . 'doc'));
            }
        }

        foreach ($input['resources'] as $from => $config) {
            foreach ($config as $name => $configResources) {
                foreach ($configResources as $resourceKey => $resourceItem) {
                    $source = $from == 'modules' // else from vendors
                        ? Module::getInstance($name)->getPath() . 'Resource/'
                        : VENDOR_DIR . $name . '/';

                    $res = $from == 'modules' // else from vendors
                        ? $name . '/' . $resourceKey . '/'
                        : 'vendor/' . $resourceKey . '/';

                    $source .= $resourceItem['path'];

                    if ($resourceItem['isCopy']) {
                        Directory::copy($source, Directory::get(RESOURCE_DIR . $res));
                    }

                    $jsResource = RESOURCE_DIR . $res . $resourceKey . '.pack.js';
                    $cssResource = RESOURCE_DIR . $res . $resourceKey . '.pack.css';

                    foreach ($resourceItem['js'] as $resource) {
                        $resources['js'][] = [
                            'source' => $source . ltrim($resource, '-'),
                            'resource' => $jsResource,
                            'url' => $resourceItem['path']
                                ? '/resource/' . $res . $resourceKey . '.pack.js'
                                : '/resource/' . $name . '/javascript.pack.js',
                            'pack' => $resource[0] != '-'
                        ];
                    }

                    $css_replace = isset($resourceItem['css_replace']) ? $resourceItem['css_replace'] : [];

                    foreach ($resourceItem['css'] as $resource) {
                        $resources['css'][] = [
                            'source' => $source . ltrim($resource, '-'),
                            'resource' => $cssResource,
                            'url' => $resourceItem['path']
                                ? '/resource/' . $res . $resourceKey . '.pack.css'
                                : '/resource/' . $name . '/style.pack.css',
                            'pack' => $resource[0] != '-',
                            'css_replace' => $css_replace
                        ];
                    }
                }
            }
        }

        $resourceName = $input['routeName'];

        $jsFile = $resourceName . '.pack.js';
        $cssFile = $resourceName . '.pack.css';

        $moduleAlias = Module::getInstance()->getAlias();

        $jsRes = $moduleAlias . '/js/';
        $cssRes = $moduleAlias . '/css/';

        $jsResource = Directory::get(RESOURCE_DIR . $jsRes) . $jsFile;
        $cssResource = Directory::get(RESOURCE_DIR . $cssRes) . $cssFile;

        $callStack = $actionContext->getFullStack();

        foreach (array_keys($callStack) as $actionClass) {
            if (file_exists($jsSource = Loader::getFilePath($actionClass, '.js', 'Resource/', false))) {
                $resources['js'][] = [
                    'source' => $jsSource,
                    'resource' => $jsResource,
                    'url' => '/resource/' . $jsRes . $jsFile,
                    'pack' => true
                ];
            }
            if (file_exists($cssSource = Loader::getFilePath($actionClass, '.css', 'Resource/', false))) {
                $resources['css'][] = [
                    'source' => $cssSource,
                    'resource' => $cssResource,
                    'url' => '/resource/' . $cssRes . $cssFile,
                    'pack' => true,
                    'css_replace' => []
                ];
            }
        }

        $jsFile = 'custom.pack.js';
        $cssFile = 'custom.pack.css';

        $jsResource = Directory::get(RESOURCE_DIR . $jsRes) . $jsFile;
        $cssResource = Directory::get(RESOURCE_DIR . $cssRes) . $cssFile;

        if (!empty($input['js'])) {
            foreach ($input['js'] as $resource) {
                $resources['js'][] =
                    [
                        'source' => Loader::getFilePath($resource, '.js', 'Resource/js/'),
                        'resource' => $jsResource,
                        'url' => '/resource/' . $jsRes . $jsFile,
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
                        'url' => '/resource/' . $cssRes . $cssFile,
                        'pack' => true,
                        'css_replace' => []
                    ];
            }
        }

        $this->pack($resources);

        return array(
            'js' => array_unique(Arrays::column($resources['js'], 'url')),
            'css' => array_unique(Arrays::column($resources['css'], 'url'))
        );
    }

    /**
     * Pack all resources in groupped files
     *
     * @param $resources
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function pack($resources)
    {
        if (!class_exists('JSMin', false) && !function_exists('jsmin')) {
            require_once(VENDOR_DIR . 'mrclay/minify/min/lib/JSMin.php');

            /**
             * Custom implementation jsmin
             *
             * @param $js
             * @return string
             */
            function jsmin($js)
            {
                return JSMin::minify($js);
            }
        }

        if (!class_exists('CSSMin', false)) {
            require_once(VENDOR_DIR . 'mrclay/minify/min/lib/CSSmin.php');
        }
        $handlers = [];

        $CSSmin = new CSSMin();

        foreach ($resources['js'] as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                Directory::get(dirname($resource['resource']));
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? jsmin(file_get_contents($resource['source']))
                : file_get_contents($resource['source']);

            fwrite($handlers[$resource['resource']], '/* Ice: ' . $resource['source'] . " */\n" . $pack . "\n\n\n");
        }

        foreach ($resources['css'] as $resource) {
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

            fwrite($handlers[$resource['resource']], '/* Ice: ' . $resource['source'] . " */\n" . $pack . "\n\n\n");
        }

        foreach ($handlers as $filePath => $handler) {
            fclose($handler);

            chmod($filePath, 0664);
            chgrp($filePath, filegroup(dirname($filePath)));
        }
    }
}