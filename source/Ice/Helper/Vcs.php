<?php
/**
 * Ice helper version control system class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Vcs
 *
 * Helper for version control systems
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Vcs
{
    const MERCURIAL = 'mercurial';
    const GIT = 'git';
    const SUBVERSION = 'subversion';

    /**
     * Init local repository
     *
     * @param $vcs
     * @param $dir
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public static function init($vcs, $dir)
    {
        switch ($vcs) {
            case VCS::MERCURIAL:
                Console::run('cd ' . $dir . ' && hg init && hg add && hg commit -m \'init\'');
                break;
            case VCS::GIT:
                Console::run('git config --global user.email "' . get_current_user() . '@example.com"');
                Console::run('git config --global user.name "' . get_current_user() . '"');
                Console::run('cd ' . $dir . ' && git init && git add . && git commit -m \'init\'');
                break;
            case VCS::SUBVERSION:
                Console::run('cd ' . $dir . ' && svn create && svn add && svn commit -m \'init\'');
                break;
            default:

        }
    }

    /**
     * Pull and update source code
     *
     * @param $vcs
     * @param $dir
     * @param $branch
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public static function checkout($vcs, $dir, $branch)
    {
        switch ($vcs) {
            case VCS::MERCURIAL:
                Console::run('cd ' . $dir . ' && hg checkout ' . $branch);
                break;
            case VCS::GIT:
                Console::run('cd ' . $dir . ' && git checkout ' . $branch);
                break;
            case VCS::SUBVERSION:
                Console::run('cd ' . $dir . ' && git checkout ' . $branch);
                break;
            default:
        }
    }

    /**
     * Return default branch
     *
     * @param  $vcs
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getDefaultBranch($vcs)
    {
        switch ($vcs) {
            case VCS::MERCURIAL:
                return 'default';
                break;
            case VCS::GIT:
                return 'master';
                break;
            case VCS::SUBVERSION:
                return 'trunk';
                break;
            default:
        }

        return '';
    }
}
