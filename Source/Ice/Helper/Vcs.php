<?php
/**
 * Ice helper version control system class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Vcs
 *
 * Helper for version control systems
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version stable_0
 * @since stable_0
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
     */
    public static function init($vcs, $dir)
    {
        switch ($vcs) {
            case VCS::MERCURIAL:
                system('cd ' . $dir . ' && hg init && hg add && hg commit -m \'init\'');
                break;
            case VCS::GIT:
                system('cd ' . $dir . ' && git init && git add . && git commit -m \'init\'');
                break;
            case VCS::SUBVERSION:
                system('cd ' . $dir . ' && svn create && svn add && svn commit -m \'init\'');
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
     */
    public static function checkout($vcs, $dir, $branch)
    {
        switch ($vcs) {
            case VCS::MERCURIAL:
                system('cd ' . $dir . ' && hg checkout ' . $branch);
                break;
            case VCS::GIT:
                system('cd ' . $dir . ' && git checkout ' . $branch);
                break;
            case VCS::SUBVERSION:
                system('cd ' . $dir . ' && git checkout ' . $branch);
                break;
            default:
        }
    }

    /**
     * Return default branch
     *
     * @param $vcs
     * @return string
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