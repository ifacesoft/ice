<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 12.01.14
 * Time: 0:24
 */

namespace ice\action;

use CSSmin;
use ice\core\action\Viewable;
use ice\core\Action;
use ice\core\Action_Context;
use ice\core\Data_Provider;
use ice\core\helper\Dir;
use ice\core\helper\Json;
use ice\core\Loader;
use ice\core\Model;
use ice\data\provider\Router;
use ice\Ice;
use ice\view\render\Php;
use JSMin;

class Html_Head_Resources extends Action implements Viewable
{
    protected $layout = '';

    const RESOURCE_TYPE_JS = 'js';
    const RESOURCE_TYPE_CSS = 'css';

    const BUFFER_DATA_PROVIDER_KEY = 'Buffer:action/';

    public static $config = array(
        'Ice' => array(
            'jquery' => array(
                'path' => 'Vendor/jquery-ui-1.10.3/',
                self::RESOURCE_TYPE_JS => array(
                    'js/jquery-1.9.1.js',
                    '-js/jquery-ui-1.10.3.custom.min.js'
                ),
                self::RESOURCE_TYPE_CSS => array(
                    '-css/smoothness/jquery-ui-1.10.3.custom.min.css'
                )
            ),
            'bootstrap' => array(
                'path' => 'Vendor/bootstrap-3.1.0/',
                self::RESOURCE_TYPE_JS => array(
                    '-js/bootstrap.min.js'
                ),
                self::RESOURCE_TYPE_CSS => array(
                    '-css/bootstrap.min.css',
                    '-css/bootstrap-theme.min.css'
                )
            ),
            'module' => array(
                'path' => null,
                self::RESOURCE_TYPE_JS => array(
                    'js/Ice.js'
                ),
                self::RESOURCE_TYPE_CSS => array(
                    'css/Ice.css'
                )
            )
        )
    );

    public static function appendJs($resource)
    {
        self::append(self::RESOURCE_TYPE_JS, $resource);
    }

    public static function appendCss($resource)
    {
        self::append(self::RESOURCE_TYPE_CSS, $resource);
    }

    private static function append($resourceType, $resource)
    {
        $dataProvider = Data_Provider::getInstance(self::BUFFER_DATA_PROVIDER_KEY . __CLASS__);

        $customResources = $dataProvider->get($resourceType);

        if (!$customResources) {
            $customResources = array();
        }

        array_push($customResources, $resource);

        $dataProvider->set($resourceType, $customResources);
    }

    protected function init(Action_Context &$context)
    {
        parent::init($context);
        $context->setViewRenderClass(Php::VIEW_RENDER_PHP_CLASS);
        $context->addDataProviderKeys(
            array(
                Router::getDefaultKey(),
                self::BUFFER_DATA_PROVIDER_KEY . __CLASS__
            )
        );
    }

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        $resources = array(
            self::RESOURCE_TYPE_JS => array(),
            self::RESOURCE_TYPE_CSS => array()
        );

        foreach ($this->getConfig()->getParams() as $moduleName => $configResources) {
            foreach ($configResources as $resourceKey => $resourceItem) {
                $source = Ice::getConfig()->getParam('modules/' . $moduleName . '/path') . 'Resource/';

                $res = $moduleName . '/' . $resourceKey . '/';

                if ($resourceItem['path']) {
                    $source .= $resourceItem['path'];
                    Dir::copy($source, Dir::get(Ice::getRootPath() . 'resource/' . $res));
                }

                $jsResource = $resourceItem['path']
                    ? Ice::getRootPath() . 'resource/' . $res . $resourceKey . '.pack.js'
                    : Ice::getRootPath() . 'resource/' . $moduleName . '/javascript.pack.js';

                $cssResource = $resourceItem['path']
                    ? Ice::getRootPath() . 'resource/' . $res . $resourceKey . '.pack.css'
                    : Ice::getRootPath() . 'resource/' . $moduleName . '/style.pack.css';

                foreach ($resourceItem[self::RESOURCE_TYPE_JS] as $resource) {
                    $resources[self::RESOURCE_TYPE_JS][] = array(
                        'source' => $source . ltrim($resource, '-'),
                        'resource' => $jsResource,
                        'url' => $resourceItem['path']
                                ? '/' . $res . $resourceKey . '.pack.js'
                                : '/' . $moduleName . '/javascript.pack.js',
                        'pack' => $resource[0] != '-'
                    );
                }

                foreach ($resourceItem[self::RESOURCE_TYPE_CSS] as $resource) {
                    $resources[self::RESOURCE_TYPE_CSS][] = array(
                        'source' => $source . ltrim($resource, '-'),
                        'resource' => $cssResource,
                        'url' => $resourceItem['path']
                                ? '/' . $res . $resourceKey . '.pack.css'
                                : '/' . $moduleName . '/style.pack.css',
                        'pack' => $resource[0] != '-'
                    );
                }
            }
        }

        $resourceName = crc32(Json::decode($input['route']['params__json'])['pattern']);

        $jsFile = $resourceName . '.pack.js';
        $cssFile = $resourceName . '.pack.css';

        $jsRes = Ice::getProject() . '/js/';
        $cssRes = Ice::getProject() . '/css/';

        $jsResource = Dir::get(Ice::getRootPath() . 'resource/' . $jsRes) . $jsFile;
        $cssResource = Dir::get(Ice::getRootPath() . 'resource/' . $cssRes) . $cssFile;

        foreach (array_keys(Action::getCallStack()) as $actionClass) {
            if (file_exists($jsSource = Loader::getFilePath($actionClass, 'Resource/js', '.js', false))) {
                $resources[self::RESOURCE_TYPE_JS][] = array(
                    'source' => $jsSource,
                    'resource' => $jsResource,
                    'url' => '/' . $jsRes . $jsFile,
                    'pack' => true
                );
            }
            if (file_exists($cssSource = Loader::getFilePath($actionClass, 'Resource/css', '.css', false))) {
                $resources[self::RESOURCE_TYPE_CSS][] = array(
                    'source' => $cssSource,
                    'resource' => $cssResource,
                    'url' => '/' . $cssRes . $cssFile,
                    'pack' => true
                );
            }
        }

        $jsFile = 'custom.pack.js';
        $cssFile = 'custom.pack.css';

        $jsResource = Dir::get(Ice::getRootPath() . 'resource/' . $jsRes) . $jsFile;
        $cssResource = Dir::get(Ice::getRootPath() . 'resource/' . $cssRes) . $cssFile;

        if (!empty($input['js'])) {
            foreach ($input['js'] as $resource) {
                $resources[self::RESOURCE_TYPE_JS][] = array(
                    'source' => Loader::getFilePath($resource, 'Resource/js', '.js'),
                    'resource' => $jsResource,
                    'url' => '/' . $jsRes . $jsFile,
                    'pack' => true
                );
            }
        }
        if (!empty($input['css'])) {
            foreach ($input['css'] as $resource) {
                $resources[self::RESOURCE_TYPE_CSS][] = array(
                    'source' => Loader::getFilePath($resource, 'Resource/css', '.css'),
                    'resource' => $cssResource,
                    'url' => '/' . $cssRes . $cssFile,
                    'pack' => true
                );
            }
        }

        $this->pack($resources);

        return array(
            self::RESOURCE_TYPE_JS => array_unique(array_column($resources[self::RESOURCE_TYPE_JS], 'url')),
            self::RESOURCE_TYPE_CSS => array_unique(array_column($resources[self::RESOURCE_TYPE_CSS], 'url'))
        );
    }

    private function pack($resources)
    {
        if (!class_exists('JSMin', false) && !function_exists('jsmin')) {
            require_once(Ice::getEnginePath() . 'Vendor/JSMin.php');

            function jsmin($js)
            {
                return JSMin::minify($js);
            }
        }

        if (!class_exists('CSSMin', false)) {
            require_once(Ice::getEnginePath() . 'Vendor/CSSmin.php');
        }
        $handlers = array();

        $CSSmin = new CSSMin();

        foreach ($resources[self::RESOURCE_TYPE_JS] as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? jsmin(file_get_contents($resource['source']))
                : file_get_contents($resource['source']);

            fwrite($handlers[$resource['resource']], '/* Ice: ' . $resource['source'] . "*/\n" . $pack . "\n\n\n");
        }

        foreach ($resources[self::RESOURCE_TYPE_CSS] as $resource) {
            if (!isset($handlers[$resource['resource']])) {
                $handlers[$resource['resource']] = fopen($resource['resource'], 'w');
            }

            $pack = $resource['pack']
                ? $CSSmin->run(file_get_contents($resource['source']))
                : file_get_contents($resource['source']);

            fwrite($handlers[$resource['resource']], '/* Ice: ' . $resource['source'] . "*/\n" . $pack . "\n\n\n");
        }

        foreach ($handlers as $handler) {
            fclose($handler);
        }
    }
}