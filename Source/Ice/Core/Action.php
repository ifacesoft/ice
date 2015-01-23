<?php
/**
 * Ice core action abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Arrays;
use Ice\Helper\Hash;
use Ice\Helper\Json;

/**
 * Class Action
 *
 * Core action abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.1
 * @since 0.0
 */
abstract class Action extends Container
{
    use Core;

    const REGISTRY_DATA_PROVIDER_KEY = 'Ice:Registry/action';

    /**
     * Default config
     *
     * @var array
     */
    public static $configDefaults = [
        'afterActions' => [],
        'layout' => null,
        'template' => null,
        'output' => null,
        'defaultViewRenderClassName' => null,
        'inputDefaults' => [],
        'inputValidators' => [],
        'inputDataProviderKeys' => [],
        'outputDataProviderKeys' => [],
        'cacheDataProviderKey' => ''
    ];

    /**
     * Overridable config
     *
     * @var array
     */
    public static $config = [];

    /**
     * Private constructor of action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function __construct()
    {
    }

    /**
     * Get action object by name
     *
     * @param $actionClass
     * @param $hash
     * @return Action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($actionClass, $hash = null)
    {
        return new $actionClass();
    }

    /**
     * Default action key
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return self::getClass();
    }

    /**
     * Calling action
     *
     * @param Action_Context $actionContext
     * @param array $data
     * @param int $level
     * @return View|null|string
     * @throws Redirect
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function call(Action_Context $actionContext, array $data = [], $level = 0)
    {
        /** @var Action $actionClass */
        $actionClass = get_class($this);

        if (Request::isCli()) {
            Action::getLogger()->info(['{$0}call: {$1}...', [str_repeat("\t", $level), $actionClass]], Logger::MESSAGE);
        }

        try {
            $config = $actionClass::getConfig();

            $input = $this->getInput($config, $data);

            if (Environment::isDevelopment()) {
                Logger::fb('action: ' . $actionClass . ' ' . Json::encode($input));
            }

            $hash = Hash::get($input, Hash::HASH_CRC32);
            $inputHash = $actionClass . '/' . $hash;

            $actionContext->initAction($actionClass, $hash);
//
            $cacheDataProviderKey = null;
            $dataProvider = null;

            if ($cacheDataProviderKey = $config->get('cacheDataProviderKey', false)) {
                $dataProvider = Data_Provider::getInstance($cacheDataProviderKey, 'cache');

                $viewData = $dataProvider->get($inputHash);

                if ($viewData) {
                    return $this->flush(View::getInstance($viewData));
                }
            }

            $actionContext->addAction($config->gets('afterActions', false));

            $params = $this->getParams($config->gets('outputDataProviderKeys', false), (array)$this->run($input, $actionContext));

            foreach ($actionContext->getActions() as $subActionName => $actionData) {
                if ($subActionName[0] == '_') {
                    $subActionName = $actionClass . $subActionName;
                }

                $newLevel = $level + 1;

                /** @var Action $action */
                $subAction = Action::getInstance($subActionName);
                /** @var Action $subActionClass */
                $subActionClass = get_class($subAction);

                foreach ($actionData as $subActionKey => $subActionParams) {
                    $subView = null;

                    try {
                        $subView = $subAction->call($actionContext, $subActionParams, $newLevel);
                    } catch (Redirect $e) {
                        throw $e;
                    } catch (Http_Bad_Request $e) {
                        throw $e;
                    } catch (Http_Not_Found $e) {
                        throw $e;
                    } catch (\Exception $e) {
                        $subView = self::getLogger()->error(['Calling subAction "{$0}" in action "{$1}" failed', [$subActionName, $actionClass]], __FILE__, __LINE__, $e);
                    }

                    if (is_int($subActionKey)) {
                        $params[$subActionClass::getClassName()][$subActionKey] = $subView;
                    } else {
                        $params[$subActionKey] = $subView;
                    }

                    $actionContext->commit();
                }
            }

//            $params['container'] = $this->getContainer();

            $actionContext->setParams($params);

            $viewData = $actionContext->getViewData();

            $template = $config->get('template', false);
            if (!empty($template)) {
                $viewData['template'] = $template;
            }

            $defaultViewRenderClassName = $config->get('defaultViewRenderClassName', false);
            if (!empty($defaultViewRenderClassName)) {
                $viewData['defaultViewRenderClassName'] = $defaultViewRenderClassName;
            }

            if (isset($input['template'])) {
                $viewData['template'] = $input['template'];
            }

            if ($cacheDataProviderKey) {
                $dataProvider->set($inputHash, $viewData);
            }

            if (Request::isCli()) {
                Action::getLogger()->info(['{$0}{$1} complete!', [str_repeat("\t", $level), $actionClass::getClassName()]], Logger::MESSAGE);
            }

            return $this->flush(View::getInstance($viewData));
        } catch (Redirect $e) {
            throw $e;
        } catch (Http_Bad_Request $e) {
            throw $e;
        } catch (Http_Not_Found $e) {
            throw $e;
        } catch (\Exception $e) {
            return Action::getLogger()->error(['Calling action "{$0}" failed', $actionClass], __FILE__, __LINE__, $e);
        }
    }

    /**
     * Gets input data from data providers
     *
     * @param Config $config
     * @param array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getInput(Config $config, array $input)
    {
        $dataProviderKeys = $config->gets('inputDataProviderKeys', false);

        /** @var Action $actionClass */
        $actionClass = get_class($this);
        $dataProviderKeys[] = $actionClass::getRegistryDataProviderKey();

        /** @var Data_Provider $dataProvider */
        $dataProvider = null;

        foreach ($dataProviderKeys as $dataProviderKey) {
            $dataProvider = Data_Provider::getInstance($dataProviderKey);
            $input += (array)$dataProvider->get();
        }

        return Validator::validateByScheme(
            Arrays::defaults($config->gets('inputDefaults', false), $input),
            $config->gets('inputValidators', false)
        );
    }

    /**
     * Return default input registry data provider for this action
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getRegistryDataProviderKey()
    {
        return self::REGISTRY_DATA_PROVIDER_KEY . get_called_class();
    }

    /**
     * Flush action context.
     *
     * Modify view after flush
     *
     * @param View $view
     * @return View
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function flush(View $view)
    {
        return $view;
    }

    /**
     * Receive params from input data providers
     *
     * @param $dataProviderKeys
     * @param array $output
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getParams($dataProviderKeys, array $output)
    {
        $dataProviderKeys = (array)$dataProviderKeys;

        /** @var Data_Provider $dataProvider */
        $dataProvider = null;

        foreach ($dataProviderKeys as $dataProviderKey) {
            $dataProvider = Data_Provider::getInstance($dataProviderKey);
            $output += (array)$dataProvider->get();
        }

        return $output;
    }

    /**
     *  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'defaultViewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     *
     * public static $config = [
     *      'defaultViewRenderClassName' => 'Ice:Php'
     * ];
     *
     * /** Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract protected function run(array $input, Action_Context $actionContext);

    /**
     * Return instance of Action
     *
     * @param null $key
     * @param null $ttl
     * @return Action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}