<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Request;
use Ice\Widget\Menu\Nav;

class Header_Menu extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'actions' => [],
            'input' => [],
            'output' => [],
            'ttl' => -1,
            'access' => [
                'roles' => [],
                'request' => null,
                'env' => null
            ]
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $host = Request::host();

        $hrefPrefix = 'http://iceframework.net';
        $target = '_blank';

        if ($host == 'www.local') {
            $hrefPrefix = '';
            $target = '_self';
        }

        $navMenu = Nav::create(Request::uri(), __CLASS__)
            ->setClasses('nav-pills')
            ->link('handbook', 'Руководство', ['href' => $hrefPrefix . '/handbook', 'target' => $target])
//            ->link('cookbook', 'Полезные статьи', ['href' => $hrefPrefix . '/cookbook', 'target' => $target])
//            ->link('blog', 'Блог', ['href' => $hrefPrefix . '/blog', 'target' => $target])
//            ->link('forum', 'Форум', ['href' => $hrefPrefix . '/forum', 'target' => $target])
            ->link('github', 'GitHub', ['href' => 'https://github.com/ifacesoft/Ice/tree/master', 'target' => $target])
            ->link('bitbucket', 'Bitbucket', ['href' => 'https://bitbucket.org/dp_ifacesoft/ice', 'target' => $target])
            ->link('api', 'Api', ['href' => $hrefPrefix . '/resource/api/Ice/1.0', 'target' => $target])
//            ->link('roadmap', 'План разработки', ['href' => $hrefPrefix . '/roadMap', 'target' => $target])
        ;

        return ['menu' => $navMenu];
    }
}