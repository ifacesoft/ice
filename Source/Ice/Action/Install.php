<?php

namespace Ice\Action;

use Ice\Core\Action;

class Install extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => null],
            'actions' => [],
            'input' => [],
            'output' => [],
            'ttl' => -1,
            'roles' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        $projectName = Console::getInteractive(
            __CLASS__,
            'ProjectName',
            'Project name in CamelCase',
            [
                'default' => 'MyProject',
                'title' => 'Module name [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ]
        );

        $alias = Console::getInteractive(
            __CLASS__,
            'alias',
            'Short project name with first uppercase letter',
            [
                'default' => 'Mp',
                'title' => 'Module alias (short module name, 2-5 letters) [{$0}]: ',
                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
            ]
        );

        $desc = Console::getInteractive(
            __CLASS__,
            'description',
            'Short project description',
            [
                'default' => 'My Ice Project',
                'title' => 'Project description [{$0}]: ',
                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
            ]
        );

        $url = Console::getInteractive(
            __CLASS__,
            'url',
            'Production project url',
            [
                'default' => 'localhost',
                'title' => 'Project url [{$0}]: ',
                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
            ]
        );

        $vcs = Console::getInteractive(
            __CLASS__,
            'url',
            'Version control system',
            [
                [
                    'default' => 'mercurial',
                    'title' => 'Default VCS (mercurial|git|subversion)  [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => '/^(mercurial|git|subversion)$/i'
                    ]
                ]
            ]
        );

        $moduleConfig = [
            'alias' => $alias,
            'module' => [
                'name' => $projectName,
                'description' => $desc,
                'url' => $url,
                'authors' => 'anonymous <email>',
                'vcs' => $vcs,
                'source' => '',
                'Ice\Core\Data_Source' => [],
                'configDir' => 'Config/',
                'sourceDir' => 'Source/',
                'resourceDir' => 'Resource/',
                'logDir' => '../_log/',
                'cacheDir' => '../_cache/',
                'uploadDir' => '../_upload/',
                'compiledResourceDir' => '../_resource/',
                'downloadDir' => '../_resource/download/',
            ],
            'vendors' => []
        ];

    }
}