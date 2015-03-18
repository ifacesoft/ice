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
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Helper\Console;
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
 * @version 0.0
 * @since 0.0
 */
class Module_Create extends Action
{
    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'input' => [
                'name' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'MyProject',
                                'title' => 'Module name [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                            ]
                        );
                    }
                ],
                'alias' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'Mp',
                                'title' => 'Module alias (short module name, 2-5 letters) [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                            ]
                        );
                    }
                ],
                'scheme' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'test',
                                'title' => 'Scheme - database name(not empty and must be exists) [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                            ]
                        );
                    }
                ],
                'username' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'root',
                                'title' => 'Database username [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                            ]
                        );
                    }
                ],
                'password' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => '',
                                'title' => 'Database username password [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                            ]
                        );
                    }
                ],
                'viewRender' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'Smarty',
                                'title' => 'Default view render (Php|Smarty|Twig)  [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^(Php|Smarty|Twig)$/i']
                            ]
                        );
                    }
                ],
                'vcs' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'mercurial',
                                'title' => 'Default version control system (mercurial|git|subversion)  [{$0}]: ',
                                'validators' => ['Ice:Pattern' => '/^(mercurial|git|subversion)$/i']
                            ]
                        );
                    }
                ],
                'isWeb' => [
                    'providers' => 'cli',
                    'default' => function ($param) {
                        return Console::getInteractive(__CLASS__, $param,
                            [
                                'default' => 'web',
                                'title' => 'Web project or ice module (web|module) [{$0}]: ',
                                'validators' => [
                                    'Ice:Pattern' => ['params' => '/^(web|module)$/i', 'message' => 'Web or Module?']
                                ]
                            ]
                        );
                    }
                ]
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function run(array $input)
    {
        $moduleName = ucfirst($input['name']);
        $moduleAlias = ucfirst($input['alias']);

        if (file_exists(ROOT_DIR . $moduleName)) {
            Module_Create::getLogger()->info(['Module {$0} already exists', $moduleName], Logger::INFO, true, false);
            $this->getView()->setTemplate('');
            return [];
        }

        // create module dir

        $config = [];
        $environment = [];
        $action = [];
        $route = [];

        if ($input['isWeb'] == 'web') {
            $defaultLocale = 'en';

            $isMultiLocale = Console::getInteractive(__CLASS__, 'isMultilocale',
                [
                    'default' => 'true',
                    'title' => 'Multilocale project (true|false) [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => ['params' => '/^(true|false)$/i', 'message' => 'True or false?']
                    ]
                ]
            );

            if ($isMultiLocale == 'true') {
                $defaultLocale = Console::getInteractive(__CLASS__, 'defaultLocale',
                    [
                        'default' => 'en',
                        'title' => 'Set default locale (en|ru) [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => ['params' => '/^(en|ru)$/i', 'message' => 'Only en and ru available']
                        ]
                    ]
                );
            }

            $config = [
                'Ice\Helper\Api_Client_Yandex_Translate' => [
                    'translateKey' => ''
                ],
                'Ice\Core\Request' => [
                    'multiLocale' => $isMultiLocale,
                    'locale' => $defaultLocale,
                ],
                'Ice\Core\Environment' => [
                    'environments' => [
                        '/' . strtolower($moduleName) . '.global$/' => 'production',
                        '/' . strtolower($moduleName) . '.test$/' => 'test',
                        '/' . strtolower($moduleName) . '.local$/' => 'development'
                    ]
                ]
            ];

            $environment = [
                'production' => [
                    'Ice\Core\Data_Provider' => [
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
                                        'path' => 'Ice/',
                                        'js' => [
                                            1 => 'Helper/String.js'
                                        ],
                                        'css' => [],
                                        'isCopy' => false,
                                    ],
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

            $route = [
                strtolower($moduleAlias) . '_main' => [
                    'route' => '/',
                    'request' => [
                        'GET' => [
                            'Ice:Layout_Main' => [
                                'actions' => [
                                    ['title' => 'Ice:Title', ['title' => $moduleAlias]],
                                    'main' => $moduleAlias . ':Index'
                                ]
                            ]
                        ]
                    ],
                    'weight' => 10000
                ]
            ];
        }

        $module = [
            'alias' => $moduleAlias,
            'module' => [
                'name' => $moduleName,
                'type' => $input['isWeb'] == 'web' ? 'web' : 'module',
                'url' => '',
                'authors' => get_current_user() . ' <email>',
                'vcs' => $input['vcs'],
                'source' => '',
                'Ice\Core\Data_Source' => [
                    'Ice:Mysql/default' => [strtolower($moduleAlias) . '_', 'ice_', ''],
                ]
            ],
            'modules' => []
        ];


        $moduleDir = Directory::get(ROOT_DIR . $moduleName);

        $iceModule = Module::getInstance('Ice');

        if ($input['isWeb'] == 'web') {
            copy($iceModule->get('path') . 'cli', $moduleDir . 'cli');
            chmod($moduleDir . 'cli', 0755);

            copy($iceModule->get('path') . 'app.php', $moduleDir . 'app.php');

            Directory::copy(Module::getInstance('Ice')->get('resourceDir') . '/web', $moduleDir . 'Web');

            copy($iceModule->get('path') . 'composer.phar', $moduleDir . 'composer.phar');

            $composer = Json::decode(file_get_contents($iceModule->get('path') . 'composer.json'));

            $composer['name'] = $moduleName;
            $composer['description'] = $moduleName;
            unset($composer['keywords']);
            unset($composer['homepage']);
            unset($composer['license']);

            File::createData($moduleDir . 'composer.json', Json::encode($composer, true), false);

            Directory::get(ROOT_DIR . '_log/' . $moduleName);
        } else {
            $this->getView()->setTemplate('');
        }

        copy($iceModule->get('path') . '.gitignore', $moduleDir . '.gitignore');
        copy($iceModule->get('path') . '.hgignore', $moduleDir . '.hgignore');

        File::createData($moduleDir . 'Config/Ice/Core/Config.php', $config);
        File::createData($moduleDir . 'Config/Ice/Core/Environment.php', $environment);
        File::createData($moduleDir . 'Config/Ice/Core/Action.php', $action);
        File::createData($moduleDir . 'Config/Ice/Core/Route.php', $route);
        File::createData($moduleDir . 'Config/Ice/Core/Module.php', $module);

        Vcs::init($input['vcs'], $moduleDir);

        Module_Create::getLogger()->info(['Module {$0} created', $moduleName], Logger::SUCCESS, true, false);

        return ['moduleName' => $moduleName];
    }
}