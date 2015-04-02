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
            $fastStep = 5;

            $pageCount = intval($this->foundRows / $this->getKey('limit')) + 1;

            $limit = $this->getKey('page') == $pageCount
                ? $this->foundRows - ($pageCount - 1) * $this->getKey('limit')
                : $this->getKey('limit');

            if ($this->getKey('page') > 1) {
                $this->link('first', 1 . ' &lt;&lt;&lt;');
            }

            if ($this->getKey('page') - $fastStep >= 1) {
                $this->link('fastPrev', ($this->getKey('page') - $fastStep) . ' &lt;&lt;');
            }

            if ($this->getKey('page') > 1) {
                $this->link('leftSep', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            }

            if ($this->getKey('page') - 2 >= 1) {
                $this->link('before2', $this->getKey('page') - 2);
            }

            if ($this->getKey('page') - 1 >= 1) {
//                $this->link('prev', ($this->getKey('page') - 1) . ' &lt;');
                $this->link('before1', $this->getKey('page') - 1);
            }

            $this->link('current', $this->getKey('page') . ' ( ' . $limit . ' / ' . $this->foundRows . ' )', ['classes' => ['active'], 'style' => 'z-index: 0;']);

            if ($this->getKey('page') + 1 <= $pageCount) {
//                $this->link('next', '&gt; ' . ($this->getKey('page') + 1));
                $this->link('after1', $this->getKey('page') + 1);
            }

            if ($this->getKey('page') + 2 <= $pageCount) {
                $this->link('after2', $this->getKey('page') + 2);
            }

            if ($this->getKey('page') < $pageCount) {
                $this->link('rightSep', ' &hellip; ', ['classes' => ['disabled'], 'style' => 'border: none;']);
            }

            if ($this->getKey('page') + $fastStep <= $pageCount) {
                $this->link('fastNext', '&gt;&gt; ' . ($this->getKey('page') + $fastStep));
            }

            if ($this->getKey('page') < $pageCount) {
                $this->link('last', '&gt;&gt;&gt; ' . $pageCount);
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