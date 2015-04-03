<?php

namespace Ice\Ui\Menu;

use Ice\Core\Ui_Menu;

class Pagination extends Ui_Menu
{
    private $foundRows = null;

    protected static function create($key)
    {
        if (!isset($key['page'])) {
            $key['page'] = 1;
        }

        return parent::create($key);
    }

    public function getOffset()
    {
        return $this->getKey('page') === 1 ? 0 : $this->getKey('page') * $this->getKey('limit');
    }

    public function getItems()
    {
        if ($this->foundRows === null) {
            Pagination::getLogger()->exception('foundRows not defined. Please use ->setFoundRows(x)', __FILE__, __LINE__);
        }

        if (parent::getItems() === null) {
            $page = $this->getKey('page');
            
            $pageCount = intval($this->foundRows / $this->getKey('limit')) + 1;

            
            $limit = $page == $pageCount
                ? $this->foundRows - ($pageCount - 1) * $this->getKey('limit')
                : $this->getKey('limit');

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

            $newPage = $page - 3;
            if ($newPage >= 1) {
                $this->link('before3', $newPage, ['page' => $newPage]);
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

            $newPage = $page + 3;
            if ($newPage <= $pageCount) {
                $this->link('after3', $newPage, ['page' => $newPage]);
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

            $newPage = $page;
            if ($newPage < $pageCount) {
                $this->link('rightSep3', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
                $this->link('last', '&gt;&gt;&gt; ' . $pageCount, ['page' => $newPage]);
            }
        }

        return parent::getItems();
    }

    public function link($name, $title, array $options = [], $template = 'Link')
    {
        return $this->addItem($name, $title, $options, $template);
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
     */
    public function setFoundRows($foundRows)
    {
        $this->foundRows = $foundRows;
    }
}