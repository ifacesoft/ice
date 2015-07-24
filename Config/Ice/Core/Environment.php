<?php
/**
 * @file Environment config
 *
 * Sets default config params for ice application components
 *
 * @author dp <denis.a.shestakov@gmail.com>
 */

return [
    'production' => [
        'Ice\Core\Data_Provider' => [
            'Ice\Data\Provider\Redis' => [
                'session' => [
                    'ttl' => 60 * 60 * 24
                ],
            ],
            'Ice\Data\Provider\File' => [
                'session' => [
                    'ttl' => 60 * 60 * 24
                ],
            ],
            'Ice\Data\Provider\Mysqli' => [
                'default' => [
                    'username' => 'root',
                    'password' => '',
                ]
            ],
            'Ice\Data\Provider\Mongodb' => [
                'default' => [
                ]
            ]
        ],
        'dataProviderKeys' => [
            'Ice\Core\Bootstrap' => [
                'instance' => 'Ice:Registry/bootstrap',
            ],
            'Ice\Core\Security' => [
                'instance' => 'Ice:Registry/security',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry/loader',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:Repository/action',
                'cache' => 'Ice:Repository/action'
            ],
            'Ice\Core\Router' => [
                'instance' => 'Ice:Registry/router',
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:Repository/route',
                'route' => 'Ice:Repository/route',
                'routes' => 'Ice:Repository/route',
            ],
            'Ice\Core\Config' => [
                'instance' => 'Ice:Repository/config',
                'config' => 'Ice:Repository/config',
                'route' => 'Ice:Repository/config',
                'routes' => 'Ice:Repository/config',
                'model_scheme' => 'Ice:Repository/config',
            ],
            'Ice\Core\Converter' => [
                'instance' => 'Ice:Repository/converter',
            ],
            'Ice\Core\Widget' => [
                'instance' => 'Ice:Repository/widget',
            ],
            'Ice\Core\Widget_Form' => [
                'instance' => 'Ice:Repository/widget_form',
            ],
            'Ice\Core\Widget_Menu' => [
                'instance' => 'Ice:Repository/widget_menu',
            ],
            'Ice\Core\Widget_Data' => [
                'instance' => 'Ice:Repository/widget_data',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:Repository/container',
            ],
            'Ice\Core\View_Render' => [
                'instance' => 'Ice:Registry/view_render',
            ],
            'Ice\Core\Query_Translator' => [
                'instance' => 'Ice:Repository/query_translator',
            ],
            'Ice\Core\Query_Scope' => [
                'instance' => 'Ice:Repository/query_scope',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:Repository/query',
                'sql' => 'Ice:Repository/query',
                'query' => 'Ice:Repository/query',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:Repository/code_generator',
            ],
            'Ice\Core\Data_Source' => [
                'instance' => 'Ice:Repository/data_source',
                'cache' => 'Ice:File/data_source'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:Repository/data_scheme',
            ],
            'Ice\Core\Model_Scheme' => [
                'model_scheme' => 'Ice:Repository/model_scheme',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:Repository/validator',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:Repository/cache',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:Repository/view',
                'view' => 'Ice:Repository/view',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:Repository/resource'
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:Repository/module'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:Repository/session'
            ]
        ],
    ],
    'test' => [
        'dataProviderKeys' => [
            'Ice\Core\Bootstrap' => [
                'instance' => 'Ice:Registry/bootstrap',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry/loader',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:File/action',
                'cache' => 'Ice:File/action'
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:File/route',
                'route' => 'Ice:File/route',
                'routes' => 'Ice:File/route',
            ],
            'Ice\Core\Config' => [
                'instance' => 'Ice:File/config',
                'config' => 'Ice:File/config',
                'route' => 'Ice:File/config',
                'routes' => 'Ice:File/config',
                'model_scheme' => 'Ice:File/config',
            ],
            'Ice\Core\Converter' => [
                'instance' => 'Ice:File/converter',
            ],
            'Ice\Core\Widget' => [
                'instance' => 'Ice:File/widget',
            ],
            'Ice\Core\Widget_Form' => [
                'instance' => 'Ice:File/widget_form',
            ],
            'Ice\Core\Widget_Menu' => [
                'instance' => 'Ice:File/widget_menu',
            ],
            'Ice\Core\Widget_Data' => [
                'instance' => 'Ice:File/widget_data',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:File/container',
            ],
            'Ice\Core\View_Render' => [
                'instance' => 'Ice:Registry/view_render',
            ],
            'Ice\Core\Query_Translator' => [
                'instance' => 'Ice:File/query_translator',
            ],
            'Ice\Core\Query_Scope' => [
                'instance' => 'Ice:File/query_scope',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:File/query',
                'sql' => 'Ice:File/query',
                'query' => 'Ice:File/query',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:File/code_generator',
            ],
            'Ice\Core\Data_Source' => [
                'instance' => 'Ice:File/data_source',
                'cache' => 'Ice:File/data_source'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:File/data_scheme',
            ],
            'Ice\Core\Model_Scheme' => [
                'model_scheme' => 'Ice:File/model_scheme',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:File/validator',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File/cache',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:File/view',
                'view' => 'Ice:File/view',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:File/resource'
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:File/module'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File/session'
            ]
        ]
    ],
    'development' => [
        'dataProviderKeys' => [
            'Ice\Core\Bootstrap' => [
                'instance' => 'Ice:Registry/bootstrap',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry/loader',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:Registry/action',
                'output' => 'Ice:Registry/action'
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:Registry/route',
                'route' => 'Ice:Registry/route',
                'routes' => 'Ice:Registry/route',
            ],
            'Ice\Core\Config' => [
                'instance' => 'Ice:Registry/config',
                'config' => 'Ice:Registry/config',
                'route' => 'Ice:Registry/config',
                'routes' => 'Ice:Registry/config',
                'model_scheme' => 'Ice:Registry/config',
            ],
            'Ice\Core\Converter' => [
                'instance' => 'Ice:Registry/converter',
            ],
            'Ice\Core\Widget' => [
                'instance' => 'Ice:Registry/widget',
            ],
            'Ice\Core\Widget_Form' => [
                'instance' => 'Ice:Registry/widget_form',
            ],
            'Ice\Core\Widget_Menu' => [
                'instance' => 'Ice:Registry/widget_menu',
            ],
            'Ice\Core\Widget_Data' => [
                'instance' => 'Ice:Registry/widget_data',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:Registry/container',
            ],
            'Ice\Core\View_Render' => [
                'instance' => 'Ice:Registry/config'
            ],
            'Ice\Core\Query_Translator' => [
                'instance' => 'Ice:Registry/query_translator',
            ],
            'Ice\Core\Query_Scope' => [
                'instance' => 'Ice:Registry/query_scope',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:Registry/query',
                'sql' => 'Ice:Registry/query',
                'query' => 'Ice:Registry/query',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:Registry/code_generator',
            ],
            'Ice\Core\Data_Source' => [
                'instance' => 'Ice:Registry/data_source',
                'cache' => 'Ice:Registry/cache'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:Registry/data_scheme',
            ],
            'Ice\Core\Model_Scheme' => [
                'model_scheme' => 'Ice:Registry/model_scheme',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:Registry/validator',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File/cache',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:Registry/view',
                'view' => 'Ice:Registry/view',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:Registry/resource',
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:Registry/module'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File/session'
            ]
        ],
    ]
];