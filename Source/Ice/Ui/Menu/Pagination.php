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

        $limit = $page == $pageCount
            ? $this->foundRows - ($pageCount - 1) * $this->getValues('limit')
            : $this->getValues('limit');

        $newPage = 1;
        if ($page > $newPage) {
            $this->link('first', $newPage . ' &lt;&lt;&lt;', ['page' => $newPage]);
            $this->link('leftSep3', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
        }

        $newPage = $page - 100;
        if ($newPage >= 1) {
            $this->link('fastPrev2', $newPage . ' &lt;&lt;', ['page' => $newPage]);
            $this->link('leftSep2', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
        }

        $newPage = $page - 10;
        if ($newPage >= 1) {
            $this->link('fastPrev', $newPage . ' &lt;', ['page' => $newPage]);
            $this->link('leftSep', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
        }

        $newPage = $page - 2;
        if ($newPage >= 1) {
            $this->link('before2', $newPage, ['page' => $newPage]);
        }

        $newPage = $page - 1;
        if ($newPage >= 1) {
            $this->link('before1', $newPage, ['page' => $newPage]);
        }

        $this->link('current', $page . ' ( ' . $limit . ' / ' . $this->foundRows . ' )', ['page' => $page, 'classes' => ['active'], 'style' => 'z-index: 0;']);

        $newPage = $page + 1;
        if ($newPage <= $pageCount) {
            $this->link('after1', $newPage, ['page' => $newPage]);
        }

        $newPage = $page + 2;
        if ($newPage <= $pageCount) {
            $this->link('after2', $newPage, ['page' => $newPage]);
        }

        $newPage = $page + 10;
        if ($newPage <= $pageCount) {
            $this->link('rightSep', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            $this->link('fastNext', '&gt; ' . $newPage, ['page' => $newPage]);
        }

        $newPage = $page + 100;
        if ($newPage <= $pageCount) {
            $this->link('rightSep2', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            $this->link('fastNext2', '&gt;&gt; ' . $newPage, ['page' => $newPage]);
        }

        $newPage = $pageCount;
        if ($page < $pageCount) {
            $this->link('rightSep3', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            $this->link('last', '&gt;&gt;&gt; ' . $newPage, ['page' => $newPage]);
        }
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
}