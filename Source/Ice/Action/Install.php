<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Module;
use Ice\Core\Route;
use Ice\DataSource\Mongodb;
use Ice\DataSource\Mysqli;
use Ice\Helper\Console;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\Json;
use Ice\Helper\Vcs;

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
            'view' => ['viewRender' => 'Ice:Php', 'layout' => ''],
            'actions' => [
                'Ice:Deploy'
            ],
            'input' => [
                'alias',
                'vcs',
                'multilocale',
                'defaultLocale',
                'viewRender',
                'driver',
                'host',
                'username',
                'password',
                'database'
            ],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => [
                'roles' => [],
                'request' => 'cli',
                'env' => null
            ]
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
        $projectName = basename(MODULE_DIR);

        if (empty($input['alias'])) {
            $input['alias'] = Console::getInteractive(
                __CLASS__,
                'alias',
                'Short project name (2-5 letters)',
                [
                    'default' => substr(ucfirst(strtolower($projectName)), 0, 3),
                    'title' => 'Module alias [{$0}]: ',
                    'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                ]
            );
        }

        if (empty($input['vcs'])) {
            $input['vcs'] = Console::getInteractive(
                __CLASS__,
                'vcs',
                'Version control system',
                [
                    'default' => 'mercurial',
                    'title' => 'Default VCS (mercurial|git|subversion) [{$0}]: ',
                    'validators' => ['Ice:Pattern' => '(mercurial|git|subversion)']
                ]
            );
        }

        if (empty($input['multilocale'])) {
            $input['multilocale'] = Console::getInteractive(
                __CLASS__,
                'multilocale',
                'Internationalization support',
                [
                    'default' => 'yes',
                    'title' => 'Use multilocale project (yes|no) [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => [
                            'params' => '/(yes|no)/i',
                            'message' => 'Yes or No?'
                        ]
                    ]
                ]
            );
        }

        if (empty($input['defaultLocale'])) {
            $input['defaultLocale'] = Console::getInteractive(
                __CLASS__,
                'defaultLocale',
                'Default locale',
                [
                    'default' => 'en',
                    'title' => 'Locale (en|ru) [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => [
                            'params' => '(en|ru)',
                            'message' => 'Only en and ru available'
                        ]
                    ]
                ]
            );
        }

        if (empty($input['viewRender'])) {
            $input['viewRender'] = Console::getInteractive(
                __CLASS__,
                'viewRender',
                'Default view render',
                [
                    'default' => 'Php',
                    'title' => 'Default view render (Php|Smarty|Twig)  [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => '(Php|Smarty|Twig)'
                    ]
                ]
            );
        }

        if (empty($input['driver'])) {
            $input['driver'] = Console::getInteractive(
                __CLASS__,
                'driver',
                'Select one of data source driver (none - not data source connection)',
                [
                    'default' => 'mysqli',
                    'title' => 'Data source driver (mysqli|mongodb|none) [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => '(mysqli|mongodb|none)'
                    ]
                ]
            );
        }

        if ($input['driver'] != 'none') {
            if (empty($input['host'])) {
                $input['host'] = Console::getInteractive(
                    __CLASS__,
                    'host',
                    'Host or ip of data source connection',
                    [
                        'default' => 'localhost',
                        'title' => 'Host [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '(.*)'
                        ]
                    ]
                );
            }

            if (empty($input['username'])) {
                $input['username'] = Console::getInteractive(
                    __CLASS__,
                    'username',
                    'Database username',
                    [
                        'default' => 'root',
                        'title' => 'Username [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '/^[a-z]+$/i'
                        ]
                    ]
                );
            }

            if (empty($input['password'])) {
                $input['password'] = Console::getInteractive(
                    __CLASS__,
                    'password',
                    'Database password',
                    [
                        'default' => '',
                        'title' => 'Password [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '(.*)'
                        ]
                    ]
                );
            }

            if (empty($input['database'])) {
                $input['database'] = Console::getInteractive(
                    __CLASS__,
                    'database',
                    'Database name',
                    [
                        'default' => 'test',
                        'title' => 'Database [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => '(\w)'
                        ]
                    ]
                );
            }
        }

        $moduleConfig['alias'] = $input['alias'];
        $moduleConfig['module'] = array_merge(
            [
                'name' => $projectName,
                'version' => '0.0',
                'description' => Json::decode(file_get_contents(MODULE_DIR . 'composer.json'))['description'],
                'authors' => 'anonymous <email>',
                'vcs' => $input['vcs'],
                'Ice\Core\DataSource' => [],
            ],
            Module::$defaultConfig['module']
        );

        $moduleConfig['modules'] = ['ifacesoft/ice' => '/ice'];

        $config = [
            'Ice\\Core\\Request' => [
                'multiLocale' => $input['multilocale'] == 'yes' ? 1 : 0,
                'locale' => $input['defaultLocale'],
                'cors' => []
            ],
            'Ice\Helper\Api_Client_Yandex_Translate' => [
                'translateKey' => null
            ],
            'Ice\Core\ViiewOld' => [
                'layout' => null,
                'viewRenderClass' => 'Ice:' . $input['viewRender']
            ],
            'Ice\Core\Environment' => [
                'environments' => [
                    '/' . strtolower($projectName) . '\\.global$/' => 'production',
                    '/' . strtolower($projectName) . '\\.test$/' => 'test',
                    '/' . strtolower($projectName) . '\\.local$/' => 'development'
                ]
            ]
        ];

        $environmentConfig = [
            'production' => [
                'Ice\\Core\\DataProvider' => [],
                'dataProviderKeys' => [],
            ]
        ];

        $dataSourceClass = Mysqli::getClass();

        if ($input['driver'] != 'none') {
            switch ($input['driver']) {
                case 'mysqli':
                    $dataSourceClass = Mysqli::getClass();
                    break;
                case 'mongodb':
                    $dataSourceClass = Mongodb::getClass();
                    break;
                default;
            }

            $moduleConfig['module']['Ice\Core\DataSource'] = [
                $dataSourceClass . '/default.' . $input['database'] => strtolower($input['alias'] . '_')
            ];

            $environmentConfig['production']['Ice\\Core\\DataProvider'] = [
                $dataSourceClass => [
                    'default' => [
                        'host' => $input['host'],
                        'username' => $input['username'],
                        'password' => $input['password'],
                    ]
                ]
            ];
        }

        $routeConfig = [
            strtolower($input['alias']) . '_main' => [
                'route' => '/',
                'request' => [
                    'GET' => [
                        'Ice:Layout_Main' => [
                            'actions' => [
                                ['Ice:Title' => 'title', ['title' => $projectName]],
                                [$input['alias'] . ':Index' => 'main']
                            ]
                        ]
                    ]
                ],
                'weight' => 10000
            ]
        ];

        Module::create(Module::getClass(), $moduleConfig)->save('Config/');
        Config::create(Config::getClass(), $config)->save('Config/');
        Environment::create(Environment::getClass(), $environmentConfig)->save('Config/');
        Route::create(Route::getClass(), $routeConfig)->save('Config/');

        $actionConfig = [
            'Ice\Action\Resource_Css' => [
                'input' => [
                    'resources' => [
                        'default' => [
                            'modules' => [
                                $input['alias'] => [
                                    'vendor_css' => [
                                        'path' => 'css/vendor/',
                                        'css' => [],
                                        'isCopy' => false,
                                    ],
                                    'vendor' => [
                                        'path' => 'vendor/',
                                        'css' => [],
                                        'isCopy' => false,
                                    ],
                                    'common' => [
                                        'path' => '',
                                        'css' => [],
                                        'isCopy' => false,
                                    ],
                                    'module' => [
                                        'path' => 'Ice/',
                                        'css' => [],
                                        'isCopy' => false,
                                    ],
                                ]
                            ],
                            'vendors' => [
                                'jquery/jquery-ui' => [
                                    'jquery' => [
                                        'path' => '/',
                                        'css' => ['-jquery-ui.min.css', '-jquery-ui.structure.min.css', '-jquery-ui.theme.min.css'],
                                        'isCopy' => true,
                                    ],
                                ],
                                'twbs/bootstrap' => [
                                    'bootstrap' => [
                                        'path' => 'dist/',
                                        'css' => ['-css/bootstrap.min.css', '-css/bootstrap-theme.min.css'],
                                        'isCopy' => true,
                                        'css_replace' => ['url(../', 'url('],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Ice\Action\Resource_Js' => [
                'input' => [
                    'resources' => [
                        'default' => [
                            'modules' => [
                                $input['alias'] => [
                                    'vendor_js' => [
                                        'path' => 'js/vendor/',
                                        'js' => [],
                                        'isCopy' => false,
                                    ],
                                    'vendor' => [
                                        'path' => 'vendor/',
                                        'js' => [],
                                        'isCopy' => false,
                                    ],
                                    'common' => [
                                        'path' => '',
                                        'js' => [],
                                        'isCopy' => false,
                                    ],
                                    'module' => [
                                        'path' => 'Ice/',
                                        'js' => [],
                                        'isCopy' => false,
                                    ],
                                ]
                            ],
                            'vendors' => [
                                'jquery/jquery-ui' => [
                                    'jquery' => [
                                        'path' => '/',
                                        'js' => ['external/jquery/jquery.js', '-jquery-ui.min.js'],
                                        'isCopy' => true,
                                    ],
                                ],
                                'twbs/bootstrap' => [
                                    'bootstrap' => [
                                        'path' => 'dist/',
                                        'js' => ['-js/bootstrap.min.js'],
                                        'isCopy' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Ice\Action\Cache_Hit' => [
                'input' => [
                    'routeNames' => [
                        'default' => [strtolower($input['alias']) . '_main']
                    ]
                ]
            ],
        ];

        File::createData(MODULE_DIR . 'Config/Ice/Core/Action.php', $actionConfig);

        copy(ICE_DIR . 'cli', MODULE_DIR . 'cli');
        chmod(MODULE_DIR . 'cli', 0755);
        copy(ICE_DIR . 'app.php', MODULE_DIR . 'app.php');
        Directory::copy(ICE_DIR . 'Web', MODULE_DIR . 'Web');

        copy(ICE_DIR . '.gitignore', MODULE_DIR . '.gitignore');
        copy(ICE_DIR . '.hgignore', MODULE_DIR . '.hgignore');

        $composer = Json::decode(file_get_contents(MODULE_DIR . 'composer.json'));

        $composer['require'] += [
            'php' => '>=5.5.0',
            'ifacesoft/ice' => '1.0.*',
//            'twbs/bootstrap' => '3.3.*',
//            'jquery/jquery-ui' => '1.11.4',
//            'mailru/fileapi' => '2.0.10',
            'firephp/firephp-core' => 'dev-master'
        ];

        $composer['repositories'] = [
//            [
//                'type' => 'package',
//                'package' => [
//                    'name' => 'jquery/jquery-ui',
//                    'version' => '1.11.4',
//                    'dist' => [
//                        'url' => 'http://jqueryui.com/resources/download/jquery-ui-1.11.4.zip',
//                        'type' => 'zip'
//                    ]
//                ]
//            ],
//            [
//                'type' => 'package',
//                'package' => [
//                    'name' => 'mailru/fileapi',
//                    'version' => '2.0.10',
//                    'dist' => [
//                        'url' => 'https://github.com/mailru/FileAPI/archive/master.zip',
//                        'type' => 'zip'
//                    ]
//                ]
//            ]
        ];

        File::createData(MODULE_DIR . 'composer.json', Json::encode($composer, JSON_PRETTY_PRINT), false);

        Module::modulesClear();

        Action::getCodeGenerator(Action::getClass($input['alias'] . ':Index'))
            ->generate(['defaultViewRenderClass' => 'Ice:' . $input['viewRender']]);

        Vcs::init($input['vcs'], MODULE_DIR);

        return [
            'moduleName' => $projectName,
            'logDir' => getLogDir(),
            'resourceDir' => getCompiledResourceDir()
        ];
    }
}