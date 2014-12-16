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
                'cache' => [
                    'path' => CACHE_DIR
                ],
                'session' => [
                    'path' => STORAGE_DIR,
                    'ttl' => 60 * 60 * 24
                ],
                'route' => [
                    'path' => CACHE_DIR
                ],
                'routes' => [
                    'path' => CACHE_DIR
                ],
                'view' => [
                    'path' => CACHE_DIR
                ],
                'tags' => [
                    'path' => CACHE_DIR
                ],
                'query' => [
                    'path' => CACHE_DIR
                ],
            ],
            'Ice\Data\Provider\Mysqli' => [
                'default' => [
                    'host' => 'localhost',
                    'port' => '3306',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8'
                ]
            ]
        ],
        'dataProviderKeys' => [
            'Ice' => [
                'instance' => 'Ice:Object/ice',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Object/loader',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:Object/action',
                'cache' => 'Ice:String/action'
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:Object/route',
                'route' => 'Ice:Object/route',
                'routes' => 'Ice:Object/route',
            ],
            'Ice\Core\Config' => [
                'config' => 'Ice:Object/config',
            ],
            'Ice\Core\Form' => [
                'instance' => 'Ice:Object/form',
            ],
            'Ice\Core\Menu' => [
                'instance' => 'Ice:Object/menu',
            ],
            'Ice\Core\Data' => [
                'instance' => 'Ice:Object/data',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:Object/container',
            ],
            'Ice\Core\View_Render' => [
                'instance' => 'Ice:Object/view_render',
            ],
            'Ice\Core\Query_Translator' => [
                'instance' => 'Ice:Object/query_translator',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:Object/query',
                'sql' => 'Ice:String/query',
                'query' => 'Ice:String/query',
            ],
            'Ice\Core\Query_Builder' => [
                'instance' => 'Ice:Object/query_builder',
            ],
            'Ice\Core\Query_Result' => [
                'instance' => 'Ice:Object/query_result',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:Object/code_generator',
            ],
            'Ice\Core\Data_Source' => [
                'instance' => 'Ice:Object/data_source',
                'cache' => 'Ice:File/data_source'
            ],
            'Ice\Core\Data_Scheme' => [
                'instance' => 'Ice:Object/data_scheme',
            ],
            'Ice\Core\Model_Scheme' => [
                'instance' => 'Ice:Object/model_scheme',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:Object/validator',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:String/cache',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:Object/view',
                'view' => 'Ice:String/view',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:Object/resource'
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:Object/module'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:String/session'
            ]
        ],
    ],
    'test' => [],
    'development' => [
        'dataProviderKeys' => [
            'Ice' => [
                'instance' => 'Ice:Registry/ice',
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
                'config' => 'Ice:Registry/config',
            ],
            'Ice\Core\Form' => [
                'instance' => 'Ice:Registry/form',
            ],
            'Ice\Core\Menu' => [
                'instance' => 'Ice:Registry/menu',
            ],
            'Ice\Core\Data' => [
                'instance' => 'Ice:Registry/data',
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
            'Ice\Core\Query' => [
                'instance' => 'Ice:Registry/query',
                'sql' => 'Ice:Registry/query',
                'query' => 'Ice:Registry/query',
            ],
            'Ice\Core\Query_Builder' => [
                'instance' => 'Ice:Registry/query_builder',
            ],
            'Ice\Core\Query_Result' => [
                'instance' => 'Ice:Registry/data',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:Registry/code_generator',
            ],
            'Ice\Core\Data_Source' => [
                'instance' => 'Ice:Registry/data_source',
                'cache' => 'Ice:Registry/cache'
            ],
            'Ice\Core\Data_Scheme' => [
                'instance' => 'Ice:Registry/data_scheme',
            ],
            'Ice\Core\Model_Scheme' => [
                'instance' => 'Ice:Registry/model_scheme',
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