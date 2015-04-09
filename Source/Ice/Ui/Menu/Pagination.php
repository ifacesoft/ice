<?php

namespace Ice\Ui\Menu;

use Ice\Core\Query_Result;
use Ice\Core\Ui_Menu;
use Ice\Helper\Json;
use Ice\View\Render\Php;

class Pagination extends Ui_Menu
{
    private $foundRows = null;

    /**
     * @return int
     */
    public function getFoundRows()
    {
        return $this->foundRows;
    }

    /**
     * @param int $foundRows
     */
    public function setFoundRows($foundRows)
    {
        $this->foundRows = $foundRows;

        $page = $this->getValues('page');

        $pageCount = intval($this->foundRows / $this->getValues('limit')) + 1;

        $this->first(1);
        $this->fastFastPrev($page - 100);
        $this->fastPrev($page - 10);
        $this->prevPrev($page - 2);
        $this->prev($page - 1);
        $this->curr($page, $pageCount);
        $this->next($page + 1, $pageCount);
        $this->nextNext($page + 2, $pageCount);
        $this->fastNext($page + 10, $pageCount);
        $this->fastFastNext($page + 100, $pageCount);
        $this->last($pageCount);


    }

    public function render()
    {
        $menuClass = get_class($this);
        $menuName = 'Menu_' . $menuClass::getClassName();

        $items = [];

        foreach ($this->getItems() as $itemName => $item) {
            $page = isset($item['options']['page'])
                ? $item['options']['page'] : 0;

            $item['name'] = $itemName;
            $item['menuName'] = $menuName;

            $item['href'] = $this->getUrl();
            $item['dataUrl'] = $this->getUrl();
            $item['dataJson'] = Json::encode($this->getParams());
            $item['dataAction'] = $this->getAction();
            $item['dataBlock'] = $this->getBlock();

            if (!isset($item['onclick'])) {
                $item['onclick'] = 'Ice_Ui_Menu.click($(this), ' . $page . '); return false;';
            }

            $items[] = Php::getInstance()->fetch($menuClass . '_' . $item['template'], $item);
        }

        return Php::getInstance()->fetch(
            Ui_Menu::getClass($menuClass),
            [
                'items' => $items,
                'menuName' => $menuName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }

    public function bind($key, $value)
    {
        if ($key == 'page' && empty($value)) {
            $value = 1;
        }

        if ($key == 'limit' && empty($value)) {
            $value = 1000;
        }

        return parent::bind($key, $value);
    }

    public function setQueryResult(Query_Result $queryResult)
    {
        $this->setFoundRows($queryResult->getFoundRows());
    }

    private function first($page)
    {
        if ($this->getValues('page') > $page) {
            $this->link(__FUNCTION__, $page . ' &lt;&lt;&lt;', ['page' => $page]);
            $this->link('after_' . __FUNCTION__, ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
        }
    }

    private function fastFastPrev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page . ' &lt;&lt;', ['page' => $page]);
            $this->link('after_' . __FUNCTION__, ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
        }
    }

    private function fastPrev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page . ' &lt;', ['page' => $page]);
            $this->link('after_' . __FUNCTION__, ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
        }
    }

    private function prevPrev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page, ['page' => $page]);
        }
    }

    private function prev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page, ['page' => $page]);
        }
    }

    private function curr($page, $pageCount)
    {
        $limit = $page == $pageCount
            ? $this->foundRows - ($pageCount - 1) * $this->getValues('limit')
            : $this->getValues('limit');

        $this->link('current', $page . ' ( ' . $limit . ' / ' . $this->foundRows . ' )', ['page' => $page, 'classes' => ['active'], 'style' => 'z-index: 0;']);
    }

    private function next($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('after1', $page, ['page' => $page]);
        }
    }

    private function nextNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('after2', $page, ['page' => $page]);
        }
    }

    private function fastNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('rightSep', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            $this->link('fastNext', '&gt; ' . $page, ['page' => $page]);
        }
    }

    private function fastFastNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('rightSep2', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            $this->link('fastNext2', '&gt;&gt; ' . $page, ['page' => $page]);
        }
    }

    private function last($page)
    {
        if ($this->getValues('page') < $page) {
            $this->link('rightSep3', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            $this->link('last', '&gt;&gt;&gt; ' . $page, ['page' => $page]);
        }
    }
}