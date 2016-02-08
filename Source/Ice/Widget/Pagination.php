<?php

namespace Ice\Widget;

use Ice\Action\Render;
use Ice\Core\QueryBuilder;
use Ice\Core\Query_Result;
use Ice\Core\Request;
use Ice\Core\Widget;
use Ice\DataProvider\Request as DataProvider_Request;

class Pagination extends Widget
{
    protected $foundRows = 0;
    protected $isShort = false;

    protected $event = null;

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'page' => ['providers' => DataProvider_Request::class, 'default' => 1],
                'limit' => ['providers' => DataProvider_Request::class, 'default' => 15]
            ],
            'output' => [],
        ];
    }

    /**
     * @return null
     */
    public function getEvent()
    {
        if ($this->event !== null) {
            return $this->event;
        }

        return [
            'action' => Render::class,
            'data' => [
                'widgets' => [
                    $this->getInstanceKey() => get_class($this)
                ]
            ]
        ];
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
        return $this;
    }

    public function render()
    {
        $limit = $this->getValue('limit');

        if (!$limit) {
            $limit = $this->foundRows;
        }

        if (ceil($this->foundRows / $limit) < 2) {
            return '';
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

        return parent::render();
    }

    public function li($name, array $options = [], $template = 'Ice\Widget\Pagination\Li')
    {
        return $this->addPart(
            $name,
            array_merge($options, ['onclick' => $this->getEvent(), 'href' => $this->getFullUrl(Request::uri(true))]),
            $template,
            __FUNCTION__
        );
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

            $title = $this->isShort ? $pageCount : '&gt;&gt;&gt; ' . $pageCount;
            $this->li(
                __FUNCTION__,
                [
                    'label' => $title,
                    'params' => ['page' => $pageCount],
                    'prev' => $isHellip ? ' &hellip; ' : '',
                    'resource' => null
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

            $title = $this->isShort ? $page : '&gt;&gt; ' . $page;
            $this->li(
                __FUNCTION__,
                [
                    'label' => $title,
                    'params' => ['page' => $page],
                    'prev' => $isHellip ? ' &hellip; ' : '',
                    'resource' => null
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

            $title = $this->isShort ? $page : '&gt; ' . $page;
            $this->li(
                __FUNCTION__,
                [
                    'label' => $title,
                    'params' => ['page' => $page],
                    'prev' => $isHellip ? ' &hellip; ' : '',
                    'resource' => null
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
            $this->li(__FUNCTION__, ['label' => $page, 'params' => ['page' => $page], 'resource' => null]);
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
            $this->li(__FUNCTION__, ['label' => $page, 'params' => ['page' => $page], 'resource' => null]);
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

        $title = $this->isShort ? $page : $page . ' ( ' . $limit . ' / ' . $this->foundRows . ' )';
        $this->li(
            __FUNCTION__,
            ['label' => $title, 'params' => ['page' => $page], 'active' => true, 'style' => 'z-index: 0;', 'resource' => null]
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
            $this->li(__FUNCTION__, ['label' => $page, 'params' => ['page' => $page], 'resource' => null]);
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
            $this->li(__FUNCTION__, ['label' => $page, 'params' => ['page' => $page], 'resource' => null]);
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

            $title = $this->isShort ? $page : $page . ' &lt;';
            $this->li(
                __FUNCTION__,
                [
                    'label' => $title,
                    'params' => ['page' => $page],
                    'next' => $isHellip ? ' &hellip; ' : '',
                    'resource' => null
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

            $title = $this->isShort ? $page : $page . ' &lt;&lt;';
            $this->li(
                __FUNCTION__,
                [
                    'label' => $title,
                    'params' => ['page' => $page],
                    'next' => $isHellip ? ' &hellip; ' : '',
                    'resource' => null
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

            $title = $this->isShort ? $page : $page . ' &lt;&lt;&lt;';
            $this->li(
                __FUNCTION__,
                [
                    'label' => $title,
                    'params' => ['page' => $page],
                    'next' => $isHellip ? ' &hellip; ' : '',
                    'resource' => null
                ]
            );
        }

        return $this;
    }

    public function setQueryResult(Query_Result $queryResult)
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

    public function getLimit() {
        return $this->getValue('limit');
    }

    public function getPage() {
        return $this->getValue('page');
    }
}
