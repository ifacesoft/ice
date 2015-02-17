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
use Ice\Core\Logger;
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
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'defaultViewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
//        'afterActions' => 'Ice:Module_Update',
        'defaultViewRenderClassName' => 'Ice:Php',
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $moduleName = ucfirst($input['name']);
        $moduleAlias = ucfirst($input['alias']);

        if (file_exists(ROOT_DIR . $moduleName)) {
            Module_Create::getLogger()->info(['Module {$0} already exists', $moduleName], Logger::INFO, true, false);
            $actionContext->setTemplate('');
            return [];
        }

        // create module dir

        $isWeb = empty($input['isWeb'])
            ? Console::getInteractive(
                __CLASS__,
                'isWeb',
                [
                    'default' => 'web',
                    'title' => 'Web project or ice module (web|module) [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => [
                            'params' => '/^(web|module)$/i',
                            'message' => 'Web or Module?'
                        ]
                    ]
                ]
            )
            : $input['isWeb'];

        $config = [];
        $environment = [];
        $action = [];
        $route = [];

        if ($isWeb == 'web') {
            $defaultLocale = 'en';

            $isMultiLocale = Console::getInteractive(
                __CLASS__,
                'isMultilocale',
                [
                    'default' => 'true',
                    'title' => 'Multilocale project (true|false) [{$0}]: ',
                    'validators' => [
                        'Ice:Pattern' => [
                            'params' => '/^(true|false)$/i',
                            'message' => 'True or false?'
                        ]
                    ]
                ]
            );

            if ($isMultiLocale == 'true') {
                $defaultLocale = Console::getInteractive(
                    __CLASS__,
                    'defaultLocale',
                    [
                        'default' => 'en',
                        'title' => 'Set default locale (en|ru) [{$0}]: ',
                        'validators' => [
                            'Ice:Pattern' => [
                                'params' => '/^(en|ru)$/i',
                                'message' => 'Only en and ru available'
                            ]
                        ]
                    ]
                );
            }

            $config = [
                'Ice\Core\Model' => [
                    'prefixes' => [
                        strtolower($moduleAlias) => $moduleAlias,
                    ]
                ],
                'Ice\Helper\Api_Client_Yandex_Translate' => [
                    'translateKey' => ''
                ],
                'Ice\Core\Data_Source' => [
                    'Ice\Data\Source\Mysqli' => [
                        'default' => $input['scheme'],
                    ],
                ],
                'Ice\Core\Request' => [
                    'multiLocale' => $isMultiLocale,
                    'locale' => $defaultLocale,
                ],
                'Ice\Core\View' => [
                    'layout' => null,
                    'defaultViewRenderClassName' => 'Ice:' . $input['viewRender']
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
                            'actions' => [
                                'title' => ['Ice:Title' => ['title' => $moduleAlias]],
                                'main' => $moduleAlias . ':Index'
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
                'url' => '',
                'authors' => get_current_user() . ' <email>',
                'vcs' => $input['vcs'],
                'source' => '',
            ],
            'modules' => []
        ];


        $moduleDir = Directory::get(ROOT_DIR . $moduleName);

        if ($isWeb == 'web') {
            copy(ICE_DIR . 'cli', $moduleDir . 'cli');
            chmod($moduleDir . 'cli', 0755);

            copy(ICE_DIR . 'app.php', $moduleDir . 'app.php');

            Directory::copy(ICE_RESOURCE_DIR . '/web', $moduleDir . 'Web');

            copy(ICE_DIR . 'composer.phar', $moduleDir . 'composer.phar');

            $composer = Json::decode(file_get_contents(ICE_DIR . 'composer.json'));

            $composer['name'] = $moduleName;
            $composer['description'] = $moduleName;
            unset($composer['keywords']);
            unset($composer['homepage']);
            unset($composer['license']);

            File::createData($moduleDir . 'composer.json', Json::encode($composer, true), false);

            Directory::get(ROOT_DIR . '_log/' . $moduleName);
        } else {
            $actionContext->setTemplate('');
        }

        copy(ICE_DIR . '.gitignore', $moduleDir . '.gitignore');
        copy(ICE_DIR . '.hgignore', $moduleDir . '.hgignore');

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