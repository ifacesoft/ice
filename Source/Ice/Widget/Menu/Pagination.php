<?php

namespace Ice\Widget\Menu;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget_Menu;

class Pagination extends Widget_Menu
{
    protected $foundRows = 0;
    private $isShort = false;

    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [
                'page' => ['providers' => 'request', 'default' => 1],
                'limit' => ['providers' => 'request', 'default' => 15],
            ],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
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
        if (ceil($this->foundRows / $this->getValue('limit')) < 2) {
            return '';
        }

        $page = $this->getValue('page');

        $pageCount = (int) ceil($this->foundRows / $this->getValue('limit'));

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

    public function item($name, $title, array $options = [], $template = 'Ice\Widget\Menu\Pagination_Item')
    {
        return $this->addPart(
            $name,
            $title,
            array_merge($options, ['onclick' => true, 'href' => $this->getUrl()]),
            $template
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
            $this->item(
                __FUNCTION__,
                $title,
                ['params' => ['page' => $pageCount], 'prev' => $isHellip ? ' &hellip; ' : '']
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
            $this->item(
                __FUNCTION__,
                $title,
                ['params' => ['page' => $page], 'prev' => $isHellip ? ' &hellip; ' : '']
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
            $this->item(
                __FUNCTION__,
                $title,
                ['params' => ['page' => $page], 'prev' => $isHellip ? ' &hellip; ' : '']
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
            $this->item(__FUNCTION__, $page, ['params' => ['page' => $page]]);
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
            $this->item(__FUNCTION__, $page, ['params' => ['page' => $page]]);
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
        $this->item(
            __FUNCTION__,
            $title,
            ['params' => ['page' => $page], 'active' => true, 'style' => 'z-index: 0;']
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
            $this->item(__FUNCTION__, $page, ['params' => ['page' => $page]]);
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
            $this->item(__FUNCTION__, $page, ['params' => ['page' => $page]]);
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
            $this->item(
                __FUNCTION__,
                $title,
                ['params' => ['page' => $page], 'next' => $isHellip ? ' &hellip; ' : '']
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
            $this->item(
                __FUNCTION__,
                $title,
                ['params' => ['page' => $page], 'next' => $isHellip ? ' &hellip; ' : '']
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
            $this->item(
                __FUNCTION__,
                $title,
                ['params' => ['page' => $page], 'next' => $isHellip ? ' &hellip; ' : '']
            );
        }

        return $this;
    }

    public function setQueryResult(Query_Result $queryResult)
    {
        $this->setFoundRows($queryResult->getFoundRows());
        return $this;
    }

    /**
     * @param boolean $isShort
     * @return Pagination
     */
    public function setIsShort($isShort)
    {
        $this->isShort = $isShort;
        return $this;
    }

    public function queryBuilderPart(Query_Builder $queryBuilder)
    {
        $queryBuilder->setPagination($this->getValue('page'), $this->getValue('limit'));
    }
}
