<?php
/**
 * Ice action module create class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Response;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\Json;
use Ice\Helper\Vcs;

/**
 * Class Module_Create
 *
 * Action create module dir, generate config and coping index file app.php
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version stable_0
 * @since stable_0
 */
class Module_Create extends Action
{
    /**  public static $config = [
     *      'staticActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Php',
        'inputDefaults' => [
            'name' => [
                'default' => 'MyProject',
                'title' => 'Module name [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ],
            'alias' => [
                'default' => 'Mp',
                'title' => 'Module alias (short module name, 2-5 letters) [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ],
            'locale' => [
                'default' => 'ru',
                'title' => 'Default locale [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ],
            'scheme' => [
                'default' => 'test',
                'title' => 'Scheme - database name(not empty and must be exists) [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ],
            'username' => [
                'default' => 'root',
                'title' => 'Database username [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ],
            'password' => [
                'default' => '',
                'title' => 'Database username password [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^[a-z]+$/i'
                ]
            ],
            'viewRender' => [
                'default' => 'Smarty',
                'title' => 'Default view render (Php|Smarty|Twig)  [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^(Php|Smarty|Twig)$/i'
                ]
            ],
            'vcs' => [
                'default' => 'mercurial',
                'title' => 'Default version control system (mercurial|git|subversion)  [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => '/^(mercurial|git|subversion)$/i'
                ]
            ]
        ]
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $moduleName = ucfirst($input['name']);
        $moduleAlias = ucfirst($input['alias']);

        if (file_exists(ROOT_DIR . $moduleName)) {
            Response::send(Module_Create::getLogger()->info(['Module {$0} already exists', $moduleName], Logger::INFO, true, false));
            Logger::clearLog();
            $actionContext->setTemplate('');
            return [];
        }

        // create module dir
        $moduleDir = Directory::get(ROOT_DIR . $moduleName);

        //copy index file
        Directory::copy(ICE_DIR . 'Web', $moduleDir . 'Web');

        $config = [
            'Ice\Core\Model' => [
                'prefixes' => [
                    strtolower($moduleAlias) => $moduleAlias,
                ]
            ],
            'Ice\Core\Data_Source' => [
                $input['scheme'] => 'Ice:Mysqli/default'
            ],
            'Ice\Core\Request' => [
                'multilocale' => false,
                'locale' => $input['locale'],
            ],
            'Ice\Core\View' => [
                'layout' => null,
                'defaultViewRenderClassName' => 'Ice:' . $input['viewRender']
            ],
            'Ice\Core\Action' => [
                'layoutActionName' => 'Ice:Layout_Main',
            ],
            'Ice\Core\Environment' => [
                'environments' => [
                    '/' . strtolower($moduleName) . '.global$/' => 'production',
                    '/' . strtolower($moduleName) . '.test$/' => 'test',
                    '/' . strtolower($moduleName) . '.local$/' => 'development'
                ]
            ]
        ];

        File::createData($moduleDir . 'Config/Ice/Core/Config.php', $config);

        $environment = [
            'production' => [
                'Ice/Core/Data_Provider' => [
                    'Ice\Data\Provider\Mysqli' => [
                        'default' => [
                            'username' => $input['username'],
                            'password' => $input['password'],
                        ]
                    ]
                ],
                'dataProviderKeys' => []
            ]
        ];

        File::createData($moduleDir . 'Config/Ice/Core/Environment.php', $environment);

        $action = [
            'Ice\Action\Resources' => [
                'inputDefaults' => [
                    'resources' => [
                        'modules' => [
                            'Ice' => [
                                'vendor_js' => [
                                    'path' => 'js/vendor/',
                                    'js' => ['-modernizr-2.8.3.min.js'],
                                    'css' => [],
                                    'isCopy' => false
                                ],
                                'vendor_css' => [
                                    'path' => 'css/vendor/',
                                    'js' => [],
                                    'css' => ['empty.css'],
                                    'isCopy' => false
                                ],
                                'module' => [
                                    'path' => '',
                                    'js' => ['js/javascript.js'],
                                    'css' => ['css/style.css'],
                                    'isCopy' => false
                                ]
                            ]
                        ],
                        'vendors' => [
                            'jquery/jquery-ui' => [
                                'jquery' => [
                                    'path' => '/',
                                    'js' => ['external/jquery/jquery.js', '-jquery-ui.min.js'],
                                    'css' => ['-jquery-ui.min.css', '-jquery-ui.structure.min.css', '-jquery-ui.theme.min.css'],
                                    'isCopy' => true,
                                ],
                            ],
                            'twbs/bootstrap' => [
                                'bootstrap' => [
                                    'path' => 'dist/',
                                    'js' => ['-js/bootstrap.min.js'],
                                    'css' => ['-css/bootstrap.min.css', '-css/bootstrap-theme.min.css'],
                                    'isCopy' => true,
                                    'css_replace' => ['url(../', 'url(']
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];

        File::createData($moduleDir . 'Config/Ice/Core/Action.php', $action);

        $route = [
            strtolower($moduleAlias) . '_main' => [
                'route' => '',
                'GET' => [
                    'actions' => [
                        'title' => ['Ice:Title' => ['title' => $moduleAlias]],
                        'main' => $moduleAlias . ':Index'
                    ],
                    'layout' => 'Ice:Layout_Main',
                ],
                'weight' => 10000
            ]
        ];

        File::createData($moduleDir . 'Config/Ice/Core/Route.php', $route);

        $module = [
            'alias' => $moduleAlias,
            'module' => [
                'name' => $moduleName,
                'url' => '',
                'authors' => get_current_user() . ' <email>',
                'vcs' => $input['vcs'],
                'source' => '',
            ],
            'modules' => [
                ICE_DIR => Vcs::getDefaultBranch($input['vcs'])
            ]
        ];

        File::createData($moduleDir . 'Config/Ice/Core/Module.php', $module);

        Vcs::init($input['vcs'], $moduleDir);

        File::createData($moduleDir . 'branch.conf.php', Vcs::getDefaultBranch($input['vcs']));

        copy(ICE_DIR . 'cli', $moduleDir . 'cli');
        chmod($moduleDir . 'cli', 0755);
        copy(ICE_DIR . 'app.php', $moduleDir . 'app.php');
        copy(ICE_DIR . 'composer.phar', $moduleDir . 'composer.phar');

        $composer = Json::decode(file_get_contents(ICE_DIR . 'composer.json'));

        $composer['name'] = $moduleName;
        $composer['description'] = $moduleName;
        unset($composer['keywords']);
        unset($composer['homepage']);
        unset($composer['license']);

        File::createData($moduleDir . 'composer.json', Json::encode($composer, true), false);

        Response::send(Module_Create::getLogger()->info(['Module {$0} created', $moduleName], Logger::SUCCESS, true, false));

        Logger::clearLog();

        return ['moduleName' => $moduleName];
    }
}