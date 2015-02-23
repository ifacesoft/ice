<?php

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
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
    private function getAction($actionClass, array $input, $level)
    {
        $startTime = Logger::microtime();

        if (Request::isCli()) {
            Action::getLogger()->info(['{$0}call: {$1}...', [str_repeat("\t", $level), $actionClass]], Logger::MESSAGE);
        }

        $this->checkResponse($input);

        $input = $actionClass::getInput($input);

        $actionCacher = Action::getCacher();
        $hash = crc32(Json::encode($input));
        $actionHash = $actionClass . '/' . $hash;

        $actionContext = Ice::getContext()->initAction($actionClass, $hash);

        /** @var Action $action */
//        $action = $actionCacher->get($actionHash);
        $action = null;
        if (!$action) {
            $action = $actionClass::create();

            $action->setInput($input);

            $output = (array)$action->run($input);

            $finishTime = Logger::microtimeResult($startTime, true);

//            if ($content = $actionContext->getContent()) {
//                Ice::getContext()->setContent(null);
//                return $content;
//            }

            $startTimeAfter = Logger::microtime();

            foreach ($action->getActions() as $actionKey => $actionData) {
                if (empty($actionData) || count($actionData) > 2) {
                    Dispatcher::getLogger()->exception(['Wrong param count ({$0})', count($actionData)], __FILE__, __LINE__, null, $actionData);
                }

                $newLevel = $level + 1;

                /** @var Action $subActionClass */
                list($subActionClass, $subActionParams) = each($actionData);

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

            $finishTimeAfter = Logger::microtimeResult($startTimeAfter);

            $action->setOutput($output);

//            $actionCacher->set($actionHash, $action, $action->getTtl());

            if (Request::isCli()) {
                Action::getLogger()->info(['{$0}{$1} complete! [{$2} + {$3}]', [str_repeat("\t", $level), $actionClass::getClassName(), $finishTime, $finishTimeAfter]], Logger::MESSAGE);
            }

            if (Environment::isDevelopment()) {
                Logger::fb('action: ' . $actionClass . ' ' . Json::encode($input) . ' [' . $finishTime . ' ' . $finishTimeAfter . ']');
            }
        }

        return $action;
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

    private function getView(Action $action)
    {
//        $viewCacher = View::getCacher();
        $viewHash = $action->getTemplate() . '/' . md5(Json::encode($action->getOutput()));

        /** @var View $view */
//        $view = $viewCacher->get($viewHash);
        $view = null;
        if (!$view) {
            $view = View::create($action);

//            $viewCacher->set($viewHash, $view, $action->getTtl());
        }

        return $view;
    }
}