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
use Ice\Data\Provider\Registry;
use Ice\Data\Provider\Repository;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Json;
use Ice\Helper\Action as Helper_Action;

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
 */
abstract class Action
{
    use Core;

    private $_input = [];
    private $_inputHash = null;

    /**
     * Private constructor of action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     * @param array $input
     */
    private function __construct(array $input)
    {
        $this->_input = $input;
        $this->_inputHash = md5(Json::encode($input));
    }

    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [];
    }

    /**
     * Get action config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getConfig()
    {
        $repository = self::getRepository();

        if ($config = $repository->get('config')) {
            return $config;
        }

        $actionClass = self::getClass();

        $config = Config::create(self::getClass(), array_merge_recursive(Config::getInstance($actionClass, null, false, -1)->gets(), $actionClass::config()));

        return $repository->set('config', $config);
    }

    /**
     * Return action repository
     *
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getRepository()
    {
        return Repository::getInstance(__CLASS__, self::getClass());
    }

    /**
     * Return action registry
     *
     * @return Registry
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getRegistry()
    {
        return Registry::getInstance(__CLASS__, self::getClass());
    }

    /**
     * Get action object by name
     *
     * @param array $data
     * @return Action
     * @throws Redirect
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function create($data = [])
    {
        $input = [];

        foreach (self::getConfig()->gets('input', false) as $dataProviderKey => $params) {
            if ($dataProviderKey == 'default') {
                foreach ((array)$params as $name => $param) {
                    if (is_int($name)) {
                        $name = $param;
                        $param = null;
                    }

                    $input[$name] = Helper_Action::getInputParam($name, isset($data[$name]) ? $data[$name] : null, $param);
                }

                continue;
            }

            $dataProvider = Data_Provider::getInstance($dataProviderKey);

            foreach ((array)$params as $name => $param) {
                if (is_int($name)) {
                    $name = $param;
                    $param = null;
                }

                $input[$name] = Helper_Action::getInputParam($name, $dataProvider->get($name), $param);
            }
        }

        if (isset($input['redirectUrl'])) {
            throw new Redirect($input['redirectUrl']);
        }

        $actionClass = self::getClass();

        return new $actionClass($input);
    }

    /**
     * Calling action
     *
     * @param Action_Context $actionContext
     * @param int $level
     * @return View|null|string
     * @throws Redirect
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function call(Action_Context $actionContext, $level = 0)
    {
        $startTime = Logger::microtime();

        if (isset($this->_input['response'])) {
            if (isset($this->_input['response']['contentType'])) {
                $actionContext->getResponse()->setContentType($this->_input['response']['contentType']);
            }

            if (isset($this->_input['response']['statusCode'])) {
                $actionContext->getResponse()->setStatusCode($this->_input['response']['statusCode']);
            }

            unset($this->_input['response']);
        }

        /** @var Action $actionClass */
        $actionClass = get_class($this);

        if (Request::isCli()) {
            Action::getLogger()->info(['{$0}call: {$1}...', [str_repeat("\t", $level), $actionClass]], Logger::MESSAGE);
        }

        try {
            $config = $actionClass::getConfig();

//            Logger::debug($config);die();

            $actionContext->initAction($actionClass, $this->_inputHash);

            $cacher = View::getCacher();

//            if ($view = $cacher->get($this->_inputHash)) {
//                return $view;
//            }

            $actionContext->addAction($config->gets('afterActions', false));

            $params = $this->getParams($config->gets('outputDataProviderKeys', false), (array)$this->run($this->_input, $actionContext));

            $finishTime = Logger::microtimeResult($startTime, true);

            if ($content = $actionContext->getContent()) {
                $actionContext->setContent(null);
                return $content;
            }

            $startTimeAfter = Logger::microtime();

            foreach ($actionContext->getActions() as $subActionName => $actionData) {
                if ($subActionName[0] == '_') {
                    $subActionName = $actionClass . $subActionName;
                }

                $newLevel = $level + 1;

                /** @var Action $subActionClass */
                $subActionClass = Action::getClass($subActionName);

                $isThread = in_array(Action_Thread::getClass(), class_parents($subActionClass));

                foreach ($actionData as $subActionKey => $subActionParams) {
                    if ($isThread) {
//                        $actionContext->initAction($subActionClass, Hash::get($subActionParams, Hash::HASH_CRC32))->commit();
                        array_walk($subActionParams, function (&$value, $key) {
                            return $value = $key . '=' . $value;
                        });
                        continue;
                    }

                    $subView = null;

                    try {
                        $subView = $subActionClass::create($subActionParams)->call($actionContext, $newLevel);
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

            $finishTimeAfter = Logger::microtimeResult($startTimeAfter);

            $actionContext->setParams($params);

            $viewData = $actionContext->getViewData();

            $template = $config->get('view/template', false);
            if (!empty($template)) {
                $viewData['template'] = $template;
            }

            $defaultViewRenderClassName = $config->get('view/viewRenderClass', false);
            if (!empty($defaultViewRenderClassName)) {
                $viewData['defaultViewRenderClassName'] = $defaultViewRenderClassName;
            }

            if (isset($this->_input['template'])) {
                $viewData['template'] = $this->_input['template'];
            }

            $view = $cacher->set($this->_inputHash, $this->flush(View::create($viewData)));

            if (Request::isCli()) {
                Action::getLogger()->info(['{$0}{$1} complete! [{$2} + {$3}]', [str_repeat("\t", $level), $actionClass::getClassName(), $finishTime, $finishTimeAfter]], Logger::MESSAGE);
            }

            if (Environment::isDevelopment()) {
                Logger::fb('action: ' . $actionClass . ' ' . Json::encode($this->_input) . ' [' . $finishTime . ' ' . $finishTimeAfter . ']');
            }

            return $view;
        } catch (Redirect $e) {
            throw $e;
        } catch (Http_Bad_Request $e) {
            throw $e;
        } catch (Http_Not_Found $e) {
            throw $e;
        } catch (\Exception $e) {
            Action::getLogger()->exception(['Calling action "{$0}" failed', $actionClass], __FILE__, __LINE__, $e);
        }

        return '';
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
            $output += (array)Data_Provider::getInstance($dataProviderKey)->get();
        }

        return $output;
    }

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     *
     *  protected static function config()
     *  {
     *      return array_merge_recursive(
     *          [
     *             'actions' => [],
     *              'view' => [
     *                  'layout' => null,
     *                  'template' => null,
     *                  'viewRenderClass' => null,
     *              ],
     *              'input' => [],
     *              'output' => [],
     *              'ttl' => 3600,
     *              'roles' => []
     *          ],
     *          parent::config()
     *      );
     *  }
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
}