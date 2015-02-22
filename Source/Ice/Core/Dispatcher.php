<?php

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Action as Helper_Action;
use Ice\Helper\Json;

class Dispatcher
{
    use Core;

    private function __construct()
    {
    }

    public static function create()
    {
        return new Dispatcher();
    }

    /**
     * @param Action $actionClass
     * @param array $input
     * @param $level
     * @return Action
     * @throws Exception
     * @throws Http_Bad_Request
     * @throws Http_Not_Found
     * @throws Redirect
     * @throws \Exception
     */
    private function getAction($actionClass, array $input, $level) {
        $this->checkResponse($input);

        $checks = $this->checkInput($input, ['actions', 'template', 'viewRenderClass', 'layout', 'ttl']);

        $actionConfig = $actionClass::getConfig();

        $input = $this->getInput($input, (array)$actionConfig->gets('input', false));

        $actionCacher = Action::getCacher();
        $actionHash = $actionClass . '/' . md5(Json::encode($input));

        /** @var Action $action */
//        $action = $actionCacher->get($actionHash);
$action = null;
        if (!$action) {
            $action = $actionClass::create();
            $action->setInput($input);

            $output = (array)$action->run($input);

            $action->setActions(array_merge($action->getActions(), (array)$checks['actions'], (array)$actionConfig->get('actions', false)));

            if ($checks['template']) {
                $action->setTemplate($checks['template']);
            } else {
                if ($action->getTemplate() === null) {
                    $action->setTemplate($actionConfig->get('view/template', false));
                }
            }

            if ($checks['viewRenderClass']) {
                $action->setViewRenderClass($checks['viewRenderClass']);
            } else {
                if ($action->getViewRenderClass() === null) {
                    $action->setViewRenderClass($actionConfig->get('view/viewRenderClass', false));
                }
            }

            if ($checks['layout']) {
                $action->setLayout($checks['layout']);
            } else {
                if ($action->getLayout() === null) {
                    $action->setLayout($actionConfig->get('view/layout', false));
                }
            }

            if ($checks['ttl']) {
                $action->setTtl($checks['ttl']);
            } else {
                if ($action->getTtl() === null) {
                    $action->setTtl($actionConfig->get('ttl', false));
                }
            }

            foreach ($action->getActions() as $actionData) {
                if (empty($actionData) || count($actionData) > 2) {
                    Dispatcher::getLogger()->exception(['Wrong param count ({$0})', count($actionData)], __FILE__, __LINE__, null, $actionData);
                }

                $newLevel = $level + 1;

                /** @var Action $subActionClass */
                list($actionKey, $subActionClass) = each($actionData);

                $subActionClass = $subActionClass[0] == '_'
                    ? $actionClass . $subActionClass
                    : Action::getClass($subActionClass);

                $subActionParams = count($actionData) == 2 ? current($actionData) : [];

                $subView = null;

                try {
                    $subView = $this->dispatch($subActionClass, $subActionParams, $newLevel);
                } catch (Redirect $e) {
                    throw $e;
                } catch (Http_Bad_Request $e) {
                    throw $e;
                } catch (Http_Not_Found $e) {
                    throw $e;
                } catch (\Exception $e) {
                    $subView = Dispatcher::getLogger()->error(['Calling subAction "{$0}" in action "{$1}" failed', [$subActionClass, $actionClass]], __FILE__, __LINE__, $e);
                }

                if (is_int($actionKey)) {
                    $output[$subActionClass::getClassName()][] = $subView;
                } else {
                    $output[$actionKey] = $subView;
                }
            }

            $action->setOutput($this->getOutput($output, (array)$actionConfig->gets('output', false)));

//            $actionCacher->set($actionHash, $action, $action->getTtl());
        }

        return $action;
    }

    /**
     * @param Action $actionClass
     * @param array $input
     * @param int $level
     * @return View
     * @throws Http_Bad_Request
     * @throws Http_Not_Found
     * @throws Redirect
     * @throws \Exception
     */
    public function dispatch($actionClass, array $input = [], $level = 0)
    {
        return $this->getView($this->getAction($actionClass, $input, $level))->getContent();
    }

    private function checkResponse(&$input)
    {
        if (isset($input['response'])) {
            if (isset($input['response']['contentType'])) {
                Ice::getResponse()->setContentType($input['response']['contentType']);
            }

            if (isset($input['response']['statusCode'])) {
                Ice::getResponse()->setStatusCode($input['response']['statusCode']);
            }

            unset($input['response']);
        }
    }

    private function checkInput(&$input, array $names)
    {
        $checks = [];

        foreach ($names as $name) {
            if (isset($input[$name])) {
                $checks[$name] = $input[$name];
                unset($input[$name]);
                continue;
            }

            $checks[$name] = null;
        }

        return $checks;
    }

    private function getInput(array $data, array $inputConfig)
    {
        $input = [];

        foreach ($inputConfig as $dataProviderKey => $params) {
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

                if (isset($input[$name])) {
                    continue;
                }

                $input[$name] = Helper_Action::getInputParam($name, $dataProvider->get($name), $param);
            }
        }

        if (isset($input['redirectUrl'])) {
            throw new Redirect($input['redirectUrl']);
        }

        return $input;
    }

    private function getOutput(array $output, array $outputConfig)
    {
        foreach ($outputConfig as $dataProviderKey => $params) {
            $dataProvider = Data_Provider::getInstance($dataProviderKey);

            foreach ((array)$params as $name => $param) {
                if (is_int($name)) {
                    $name = $param;
                    $param = null;
                }

                $output[$name] = $dataProvider->get($name);
            }
        }

        return $output;
    }

    private function getView(Action $action)
    {
        $viewCacher = View::getCacher();
        $viewHash = $action->getTemplate() . '/' . md5(Json::encode($action->getOutput()));

        /** @var View $view */
        $view = $viewCacher->get($viewHash);

        if (!$view) {
            $view = View::create($action);
            $view->getContent();

            $viewCacher->set($viewHash, $view, $action->getTtl());
        }

        return $view;
    }
}