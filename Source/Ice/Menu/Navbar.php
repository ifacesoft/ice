<?php

namespace Ice\Menu;

use Ice\Core\Menu;

class Navbar extends Menu
{
    /**
     * Add menu dropdown item
     *
     * @param $title
     * @param $links
     * @param $position
     * @param $isActive
     * @param $template
     * @return Navbar
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function dropdown($title, array $links, $position = 'left', $isActive = true, $template = 'Ice:Navbar_Dropdown')
    {
        return $this->addItem('dropdown', $title, $links, $position, $isActive, $template);
    }

    /**
     * Add menu link item
     *
     * @param $title
     * @param $url
     * @param $position
     * @param $isActive
     * @param $template
     * @return Navbar
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function link($title, $url, $position = 'left', $isActive = true, $template = 'Ice:Navbar_Link')
    {
        return $this->addItem('link', $title, $url, $position, $isActive, $template);
    }

    /**
     * Add menu button item
     *
     * @param $title
     * @param $onclick
     * @param $position
     * @param $isActive
     * @param $template
     * @return Navbar
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function button($title, $onclick, $position = 'left', $isActive = true, $template = 'Ice:Navbar_Button')
    {
        return $this->addItem('button', $title, $onclick, $position, $isActive, $template);
    }

    /**
     * Return instance of navbar menu
     *
     * @param null $key
     * @param null $ttl
     * @return Navbar
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}