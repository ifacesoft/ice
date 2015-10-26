<?php
namespace Ice\Core;

use Ice\Widget\Menu\Pagination;
use PHPUnit_Framework_TestCase;

class WidgetTest extends PHPUnit_Framework_TestCase
{
    public function testMenuNavCrud()
    {
        $navMenu = Nav::create('/', __CLASS__)
            ->addClasses('nav-pills nav-stacked')
            ->link('/test', 'test item')
            ->link('/test2', 'test item2', ['active' => true]);

        $this->assertEquals(
            $navMenu->render(),
            '<ul class="nav nav-pills nav-stacked"
    >

            <li>
    <a href="#/test">test item</a>
</li>            <li class="active">
    <a href="#/test2">test item2</a>
</li>    </ul>
'
        );
    }

    public function testMenuNavbarCrud()
    {
        $navbarMenu = Navbar::create('/', __CLASS__)
            ->addClasses('navbar-default navbar-fixed-top')
            ->link('/test', 'test item')
            ->link('/test2', 'test item2', ['active' => true])
            ->link('/test3', 'test item3', ['position' => 'left'])
            ->link('/test4', 'test item4', ['position' => 'right']);

        $this->assertEquals(
            $navbarMenu->render(),
            '<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="collapse navbar-collapse">
                                        <ul class="nav navbar-nav ">
                                            <li>
    <a href="/test">test item</a>
</li>                                            <li class="active">
    <a href="/test2">test item2</a>
</li>                                    </ul>
                            <ul class="nav navbar-nav  navbar-left">
                                            <li>
    <a href="/test3">test item3</a>
</li>                                    </ul>
                            <ul class="nav navbar-nav  navbar-right">
                                            <li>
    <a href="/test4">test item4</a>
</li>                                    </ul>
                    </div>
    </div>
</nav>
'
        );
    }

    public function testMenuPaginationCrud()
    {
        /** @var Pagination $paginationMenu */
        $paginationMenu = Pagination::create('/', __CLASS__)
            ->addClasses('pagination-sm');

        $paginationMenu->bind([
            'page' => 5,
            'limit' => 10
        ])->setFoundRows(100);

        $this->assertEquals(
            $paginationMenu->render(),
            '<ul class="pagination pagination-sm"
    >

            <li id="Menu_Pagination_first"
    >
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 1); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       >
        1 &lt;&lt;&lt;    </a>
</li>            <li id="Menu_Pagination_after_first"
    class="disabled">
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 0); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       style="border: none;">
         &hellip;     </a>
</li>            <li id="Menu_Pagination_prevPrev"
    >
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 3); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       >
        3    </a>
</li>            <li id="Menu_Pagination_prev"
    >
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 4); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       >
        4    </a>
</li>            <li id="Menu_Pagination_current"
    class="active">
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 5); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       style="z-index: 0;">
        5 ( 10 / 100 )    </a>
</li>            <li id="Menu_Pagination_after1"
    >
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 6); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       >
        6    </a>
</li>            <li id="Menu_Pagination_after2"
    >
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 7); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       >
        7    </a>
</li>            <li id="Menu_Pagination_rightSep3"
    class="disabled">
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 0); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       style="border: none;">
         &hellip;     </a>
</li>            <li id="Menu_Pagination_last"
    >
    <a href="/" onclick=\'Ice_Widget_Menu.click($(this), 10); return false;\'
       data-url=\'/\'
       data-json=\'{"page":5,"limit":10}\'
       data-action=\'Ice\Core\WidgetTest\'
       data-view=\'WidgetTest\'
       >
        &gt;&gt;&gt; 10    </a>
</li>    </ul>
'
        );
    }
}