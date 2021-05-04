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
use Ice\Code\Generator\Action as CodeGenerator_Action;

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
     * @param array $input
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

        if (empty($input['vendor'])) {
            $input['vendor'] = Console::getInteractive(
                __CLASS__,
                'vendor',
                'Vendor)',
                [
                    'default' => 'my_organization',
                    'title' => 'Vendor name [{$0}]: ',
                    'validators' => ['Ice:Pattern' => '/^[a-z]+$/i']
                ]
            );
        }

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

        if (empty($input['namespace'])) {
            $input['namespace'] = Console::getInteractive(
                __CLASS__,
                'namespace',
                'Module/project namespace',
                [
                    'default' => ucfirst(strtolower($projectName)),
                    'title' => 'Namespace [{$0}]: ',
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
        $moduleConfig['namespace'] = $input['namespace'];
        $moduleConfig['module'] = array_merge(
            [
                'vendor' => $input['vendor'],
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
            $input['vendor'] . '_' . strtolower($input['alias']) . '_main' => [
                'route' => '/',
                'request' => [
                    'GET' => [
                        'actionClass' => 'Ice:Render',
                        'params' => [
                            'content' => [
                                'Ice:Layout',
                                [
                                    'title' => ['Ice:Title', ['title' => $projectName]],
                                    'main' => $input['alias'] . ':Index'
                                ]
                            ]
                        ]
                    ]
                ],
                'weight' => 10000
            ]
        ];

        Module::create(Module::getClass(), $moduleConfig)->save('config/');
        Config::create(Config::getClass(), $config)->save('config/');
        Environment::create(Environment::getClass(), $environmentConfig)->save('config/');
        Route::create(Route::getClass(), $routeConfig)->save('config/');

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

        File::createData(MODULE_DIR . 'config/Ice/Core/Action.php', $actionConfig);

//        copy(ICE_DIR . 'ice', MODULE_DIR . 'ice');
//        chmod(MODULE_DIR . 'ice', 0755);
//        copy(ICE_DIR . 'app.php', MODULE_DIR . 'app.php');

        Directory::copy(ICE_DIR . 'public', MODULE_DIR . 'public');

        file_put_contents(MODULE_DIR . 'public/index.php', "<?php " . PHP_EOL . "require_once '../vendor/ifacesoft/ice/public/index.php';");

        copy(ICE_DIR . '.gitignore', MODULE_DIR . '.gitignore');
        copy(ICE_DIR . '.hgignore', MODULE_DIR . '.hgignore');

        $composer = Json::decode(file_get_contents(MODULE_DIR . 'composer.json'));

        $composer['require'] += [
            'php' => '>=5.6.0',
            'ifacesoft/ice' => '1.18.*',
//            'twbs/bootstrap' => '3.3.*',
//            'jquery/jquery-ui' => '1.11.4',
//            'mailru/fileapi' => '2.0.10',
//            'firephp/firephp-core' => 'dev-master'
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

        Module::init();

        CodeGenerator_Action::getInstance(Action::getClass($input['alias'] . ':Index'))
            ->generate([
                'defaultViewRenderClass' => 'Ice:' . $input['viewRender'],
                'alias' => $input['alias']
            ]);

//        CodeGenerator_Widget::getInstance(Action::getClass($input['alias'] . ':Index'))
//            ->generate([
//                'defaultViewRenderClass' => 'Ice:' . $input['viewRender'],
//                'alias' => $input['alias']
//            ]);

        Vcs::init($input['vcs'], MODULE_DIR);

        return [
            'moduleName' => $projectName,
            'logDir' => getLogDir(),
            'resourceDir' => getCompiledResourceDir()
        ];
    }
}