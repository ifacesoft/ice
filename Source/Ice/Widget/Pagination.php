<?php

namespace Ice\Widget;

use Ice\Action\Render;
use Ice\Core\Debuger;
use Ice\Core\QueryBuilder;
use Ice\Core\QueryResult;
use Ice\Core\Request;
use Ice\Core\Router;
use Ice\Core\Widget;
use Ice\DataProvider\Request as DataProvider_Request;
use Ice\DataProvider\Router as DataProvider_Router;
use Ice\Exception\Error;
use Ice\WidgetComponent\HtmlTag;

class Pagination extends Widget
{
    protected $foundRows = 0;
    protected $isShort = false;

    protected $route = null;
    protected $event = null;

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => __CLASS__],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'page' => ['providers' => [DataProvider_Router::class, DataProvider_Request::class], 'default' => 1],
                'limit' => ['providers' => [DataProvider_Router::class, DataProvider_Request::class], 'default' => 15]
            ],
            'output' => [],
        ];
    }

    /**
     * @return array
     */
    public function getRoute()
    {
        if ($this->route !== null) {
            return $this->route;
        }

        $this->setRoute($this->getRenderRoute());

        return $this->route;
    }

    /**
     * @param null $route
     * @return array
     * @throws Error
     */
    public function setRoute($route)
    {
        if (is_string($route)) {
            $route = ['name' => $route];
        }

        if (!isset($route['name'])) {
            throw new Error('Route name not defined');
        }

        if (isset($route['params'])) {
            $route['params'] = array_merge($_GET, $route['params']); //todo: костыль,
        } else {                                                     // пока
            $route['params'] = $_GET;                                // так
        }

        if (!array_key_exists('withGet', $route)) {
            $route['withGet'] = true;
        }

        if (!array_key_exists('method', $route)) {
            $route['method'] = 'GET';
        }

        $this->route = $route;

        return $this;
    }

    /**
     * @return null
     */
    public function getEvent()
    {
        if ($this->event !== null) {
            return $this->event;
        }

        $this->setEvent($this->getRenderEvent());

        return $this->event;
    }

    /**
     * @param array $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return int
     */
    public function getFoundRows()
    {
        return $this->foundRows;
    }

    /**
     * @param int $foundRows
     * @return Pagination
     */
    public function setFoundRows($foundRows)
    {
        $this->foundRows = $foundRows;

        $limit = $this->getValue('limit');

        if (!$limit) {
            $limit = $this->foundRows;
        }

        if (ceil($this->foundRows / $limit) < 2) {
            return;
        }

        $page = $this->getValue('page');

        $pageCount = (int)ceil($this->foundRows / $limit);

        if ($page > $pageCount) {
            $page = $pageCount;
        }

        $this->first(1, $page)
            ->fastFastPrev($page - 100, $page)
            ->fastPrev($page - 10, $page)
            ->prevPrev($page - 2)
            ->prev($page - 1)
            ->curr($page, $pageCount)
            ->next($page + 1, $pageCount)
            ->nextNext($page + 2, $pageCount)
            ->fastNext($page + 10, $pageCount)
            ->fastFastNext($page + 100, $pageCount)
            ->last($page, $pageCount);
    }

    public function li($name, array $options = [], $template = 'Ice\Widget\Pagination\Li')
    {
        $route = $this->getRoute();

        $route['params']['page'] = $options['params']['page'];
        $route['params']['limit'] = $this->get('limit');

        $options['params'] = array_merge($options['params'], $route['params']);

        $options['excel'] = ['rowVisible' => false];

        return $this->addPart(new HtmlTag(
            $name,
            array_merge($options, ['route' => $route, 'onclick' => $this->getEvent()]),
            $template,
            $this
        ));
    }

    /**
     * @param $page
     * @param $pageCount
     * @return Pagination
     */
    private function last($page, $pageCount)
    {
        if ($page < $pageCount) {
            $isHellip =
                $pageCount - ($page + 100) != 1 &&
                $pageCount - ($page + 10) != 1 &&
                $pageCount - ($page + 2) != 1 &&
                $pageCount - ($page + 1) != 1 &&
                $pageCount - ($page + 0) != 1;

            $this->li(
                __FUNCTION__,
                [
                    'value' => $this->isShort ? '{$page}' : '&gt;&gt;&gt; {$page}',
                    'encode' => false,
                    'params' => ['page' => $pageCount],
                    'prev' => $isHellip ? ' &hellip; ' : '',
                    'valueResource' => true,
                    'active' => false

                ]
            );
        }

        return $this;
    }

    /**
     * @param $page
     * @param $pageCount
     * @return Pagination
     */
    private function fastFastNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $isHellip =
                $pageCount - ($page + 10) != 1 &&
                $pageCount - ($page + 2) != 1 &&
                $pageCount - ($page + 1) != 1 &&
                $pageCount - ($page + 0) != 1;

            $this->li(
                __FUNCTION__,
                [
                    'value' => $this->isShort ? '{$page}' : '&gt;&gt; {$page}',
                    'encode' => false,
                    'params' => ['page' => $page],
                    'prev' => $isHellip ? ' &hellip; ' : '',
                    'valueResource' => true,
                    'active' => false
                ]
            );
        }

        return $this;
    }

    /**
     * @param $page
     * @param $pageCount
     * @return Pagination
     */
    private function fastNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $isHellip = $pageCount - ($page + 2) != 1 &&
                $pageCount - ($page + 1) != 1 &&
                $pageCount - ($page + 0) != 1;

            $this->li(
                __FUNCTION__,
                [
                    'value' => $this->isShort ? '{$page}' : '&gt; {$page}',
                    'encode' => false,
                    'params' => ['page' => $page],
                    'prev' => $isHellip ? ' &hellip; ' : '',
                    'valueResource' => true,
                    'active' => false
                ]
            );
        }

        return $this;
    }

    /**
     * @param $page
     * @param $pageCount
     * @return Pagination
     */
    private function nextNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->li(__FUNCTION__, ['value' => '{$page}', 'params' => ['page' => $page], 'valueResource' => true, 'active' => false]);
        }

        return $this;
    }

    /**
     * @param $page
     * @param $pageCount
     * @return Pagination
     */
    private function next($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->li(__FUNCTION__, ['value' => '{$page}', 'params' => ['page' => $page], 'valueResource' => true, 'active' => false]);
        }

        return $this;
    }

    /**
     * @param $page
     * @param $pageCount
     * @return Pagination
     */
    private function curr($page, $pageCount)
    {
        $limit = $page == $pageCount
            ? $this->foundRows - ($pageCount - 1) * $this->getValue('limit')
            : $this->getValue('limit');

        $this->li(
            __FUNCTION__,
            [
                'value' => $this->isShort ? '{$page}' : '{$page} ( {$limit} / {$foundRows} )',
                'params' => ['page' => $page, 'limit' => $limit, 'foundRows' => $this->foundRows],
                'active' => true,
                'style' => 'z-index: 0;',
                'valueResource' => true
            ]
        );

        return $this;
    }

    /**
     * @param $page
     * @return Pagination
     */
    private function prev($page)
    {
        if ($page > 1) {
            $this->li(__FUNCTION__, ['value' => '{$page}', 'params' => ['page' => $page], 'valueResource' => true, 'active' => false]);
        }

        return $this;
    }

    /**
     * @param $page
     * @return Pagination
     */
    private function prevPrev($page)
    {
        if ($page > 1) {
            $this->li(__FUNCTION__, ['value' => '{$page}', 'params' => ['page' => $page], 'valueResource' => true, 'active' => false]);
        }

        return $this;
    }

    /**
     * @param $page
     * @param $currentPage
     * @return Pagination
     */
    private function fastPrev($page, $currentPage)
    {
        if ($page > 1) {
            $isHellip =
                $currentPage - 0 - $page != 1 &&
                $currentPage - 1 - $page != 1 &&
                $currentPage - 2 - $page != 1 &&
                $currentPage - 10 - $page != 1 &&
                $currentPage - 100 - $page != 1;

            $this->li(
                __FUNCTION__,
                [
                    'value' => $this->isShort ? '{$page}' : '{$page} &lt;',
                    'encode' => false,
                    'params' => ['page' => $page],
                    'next' => $isHellip ? ' &hellip; ' : '',
                    'valueResource' => true,
                    'active' => false
                ]
            );
        }

        return $this;
    }

    /**
     * @param $page
     * @param $currentPage
     * @return Pagination
     */
    private function fastFastPrev($page, $currentPage)
    {
        if ($page > 1) {
            $isHellip =
                $currentPage - 0 - $page != 1 &&
                $currentPage - 1 - $page != 1 &&
                $currentPage - 2 - $page != 1 &&
                $currentPage - 10 - $page != 1 &&
                $currentPage - 100 - $page != 1;

            $this->li(
                __FUNCTION__,
                [
                    'value' => $this->isShort ? '{$page}' : '{$page} &lt;&lt;',
                    'encode' => false,
                    'params' => ['page' => $page],
                    'next' => $isHellip ? ' &hellip; ' : '',
                    'valueResource' => true,
                    'active' => false
                ]
            );
        }

        return $this;
    }

    /**
     * @param $page
     * @param $currentPage
     * @return Pagination
     */
    private function first($page, $currentPage)
    {
        if ($this->getValue('page') > $page) {
            $isHellip =
                $currentPage - 0 - $page != 1 &&
                $currentPage - 1 - $page != 1 &&
                $currentPage - 2 - $page != 1 &&
                $currentPage - 10 - $page != 1 &&
                $currentPage - 100 - $page != 1;


            $this->li(
                __FUNCTION__,
                [
                    'value' => $this->isShort ? '{$page}' : '{$page} &lt;&lt;&lt;',
                    'encode' => false,
                    'params' => ['page' => $page],
                    'next' => $isHellip ? ' &hellip; ' : '',
                    'valueResource' => true,
                    'active' => false
                ]
            );
        }

        return $this;
    }

    public function setQueryResult(QueryResult $queryResult)
    {
        parent::setQueryResult($queryResult);

        $this->setFoundRows($queryResult->getFoundRows());
    }

    /**
     * @param boolean $isShort
     * @return $this
     */
    public function setIsShort($isShort)
    {
        $this->isShort = $isShort;
        return $this;
    }

    public function queryBuilderPart(QueryBuilder $queryBuilder, array $input)
    {
        parent::queryBuilderPart($queryBuilder, $input);

        $queryBuilder->setPagination($this->getValue('page'), $this->getValue('limit'));
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }

    public function getLimit()
    {
        return $this->getValue('limit');
    }

    public function getPage()
    {
        return $this->getValue('page');
    }
}
