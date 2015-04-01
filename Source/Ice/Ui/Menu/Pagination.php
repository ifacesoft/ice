<?php

namespace Ice\Ui\Menu;

use Ice\Core\Ui_Menu;

class Pagination extends Ui_Menu
{
    protected static function create($key)
    {
        $fastStep = 5;

        $key['pageCount'] = intval($key['foundRows'] / $key['limit']) + 1;

        if ($key['page'] == $key['pageCount']) {
            $key['limit'] = $key['foundRows'] - ($key['pageCount'] - 1) * $key['limit'];
        }

        /** @var Pagination $menu */
        $menu = parent::create($key);

        if ($key['page'] > 1) {
            $menu->link('first', 1, $key);
        }

        if ($key['page'] - $fastStep >= 1) {
            $menu->link('fastPrev', $key['page'] - $fastStep, $key);
        }

        if ($key['page'] - 2 >= 1) {
            $menu->link('before2', $key['page'] - 2, $key);
        }

        if ($key['page'] - 1 >= 1) {
            $menu->link('prev', $key['page'] - 1, $key);
            $menu->link('before1', $key['page'] - 1, $key);
        }

        if ($key['page'] + 1 <= $key['pageCount']) {
            $menu->link('next', $key['page'] + 1, $key);
            $menu->link('after1', $key['page'] + 1, $key);
        }

        if ($key['page'] + 2 <= $key['pageCount']) {
            $menu->link('after2', $key['page'] + 2, $key);
        }

        if ($key['page'] + $fastStep <= $key['pageCount']) {
            $menu->link('fastNext', $key['page'] + $fastStep, $key);
        }

        if ($key['page'] < $key['pageCount']) {
            $menu->link('last', $key['pageCount'], $key);
        }

        return $menu;
    }

    public function link($name, $title, $options = null, $template = 'Item_Link')
    {
        return $this->addItem($name, $title, $options, $template);
    }
}