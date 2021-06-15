<?php

namespace Ice\Widget\Menu;

use Ice\Core\Query_Result;
use Ice\Core\Widget_Menu;
use Ice\Helper\Json;
use Ice\Render\Php;

class Pagination extends Widget_Menu
{
    protected $foundRows = 0;

    /**
     * Pagination constructor.
     */
    protected function __construct()
    {
        $this->bind('page', 1);
        $this->bind('limit', 1000);
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

    protected function addItem($name, $title, array $options, $template)
    {
        if (!isset($options['disable']) || !$options['disable']) {
            $this->items[$name] = [
                'title' => $title,
                'options' => $options,
                'template' => $template
            ];
        }

        return $this;
    }

    /**
     * Create paginationMenu
     *
     * @param $url
     * @param $action
     * @param null $block
     * @param null $event
     * @return Pagination
     */
    public static function create($url, $action, $block = null, $event = null)
    {
        return parent::create($url, $action, $block, $event);
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
        $this->buildItems();

        $menuClass = get_class($this);
        $menuName = 'Menu_' . $menuClass::getClassName();

        $items = [];

        foreach ($this->getItems() as $itemName => $item) {
            $page = isset($item['options']['page'])
                ? $item['options']['page'] : 0;

            $item['name'] = $itemName;
            $item['menuName'] = $menuName;

            $params = $this->getParams();
            $params['page'] = $page;

            $url = $this->getUrl() . '?' . http_build_query($params);

            $item['href'] = $url;
            $item['dataUrl'] = $url;
            $item['dataJson'] = Json::encode($this->getParams());
            $item['dataAction'] = $this->getAction();
            $item['dataBlock'] = $this->getBlock();

            if (!isset($item['onclick'])) {
                $item['onclick'] = 'Ice_Widget_Menu.click($(this)); return false;';
            }

            $items[] = Php::getInstance()->fetch($menuClass . '_' . $item['template'], $item);
        }

        return Php::getInstance()->fetch(
            Widget_Menu::getClass($menuClass),
            [
                'items' => $items,
                'menuName' => $menuName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }

    private function buildItems()
    {
        if ($this->items === null) {
            $page = $this->getValues('page');

            $pageCount = ceil($this->foundRows / $this->getValues('limit'));

            $this->first(1)
                ->fastFastPrev($page - 100)
                ->fastPrev($page - 10)
                ->prevPrev($page - 2)
                ->prev($page - 1)
                ->curr($page, $pageCount)
                ->next($page + 1, $pageCount)
                ->nextNext($page + 2, $pageCount)
                ->fastNext($page + 10, $pageCount)
                ->fastFastNext($page + 100, $pageCount)
                ->last($pageCount);
        }
    }

    private function last($page)
    {
        if ($this->getValues('page') < $page) {
            $this->link('rightSep3', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none; margin-left: 0; background-color: inherit;']);
            $this->link('last', '&gt;&gt;&gt; ' . $page, ['page' => $page]);
        }

        return $this;
    }

    private function fastFastNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('rightSep2', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none; margin-left: 0; background-color: inherit;']);
            $this->link('fastNext2', '&gt;&gt; ' . $page, ['page' => $page]);
        }

        return $this;
    }

    private function fastNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('rightSep', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none; margin-left: 0; background-color: inherit;']);
            $this->link('fastNext', '&gt; ' . $page, ['page' => $page]);
        }

        return $this;
    }

    private function nextNext($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('after2', $page, ['page' => $page]);
        }

        return $this;
    }

    private function next($page, $pageCount)
    {
        if ($page < $pageCount) {
            $this->link('after1', $page, ['page' => $page]);
        }

        return $this;
    }

    private function curr($page, $pageCount)
    {
        $limit = $page == $pageCount
            ? $this->foundRows - ($pageCount - 1) * $this->getValues('limit')
            : $this->getValues('limit');

        $this->link('current', $page . ' ( ' . $limit . ' / ' . $this->foundRows . ' )', ['page' => $page, 'classes' => ['active'], 'style' => 'z-index: 0;']);

        return $this;
    }

    private function prev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page, ['page' => $page]);
        }

        return $this;
    }

    private function prevPrev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page, ['page' => $page]);
        }

        return $this;
    }

    private function fastPrev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page . ' &lt;', ['page' => $page]);
            $this->link('after_' . __FUNCTION__, ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none; margin-left: 0; background-color: inherit;']);
        }

        return $this;
    }

    private function fastFastPrev($page)
    {
        if ($page > 1) {
            $this->link(__FUNCTION__, $page . ' &lt;&lt;', ['page' => $page]);
            $this->link('after_' . __FUNCTION__, ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none; margin-left: 0; background-color: inherit;']);
        }

        return $this;
    }

    private function first($page)
    {
        if ($this->getValues('page') > $page) {
            $this->link(__FUNCTION__, $page . ' &lt;&lt;&lt;', ['page' => $page]);
            $this->link('after_' . __FUNCTION__, ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none; margin-left: 0; background-color: inherit;']);
        }

        return $this;
    }

    public function setQueryResult(Query_Result $queryResult)
    {
        $this->setFoundRows($queryResult->getFoundRows());
        return $this;
    }

    public function setLimit($limit)
    {
        $this->bind('limit', $limit);

        return $this;
    }

    public function setPage($page)
    {
        $this->bind('page', $page);

        return $this;
    }
}
