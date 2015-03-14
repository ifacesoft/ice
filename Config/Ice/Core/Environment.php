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
            'Ice' => [
                'instance' => 'Ice:Repository/ice',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Repository/loader',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:Repository/action',
                'cache' => 'Ice:Repository/action'
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:Repository/route',
                'route' => 'Ice:Repository/route',
                'routes' => 'Ice:Repository/route',
            ],
            'Ice\Core\Config' => [
                'config' => 'Ice:Repository/config',
            ],
            'Ice\Core\Form' => [
                'instance' => 'Ice:Repository/form',
            ],
            'Ice\Core\Menu' => [
                'instance' => 'Ice:Repository/menu',
            ],
            'Ice\Core\Data' => [
                'instance' => 'Ice:Repository/data',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:Repository/container',
            ],
            'Ice\Core\View_Render' => [
                'instance' => 'Ice:Repository/view_render',
            ],
            'Ice\Core\Query_Translator' => [
                'instance' => 'Ice:Repository/query_translator',
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
            'Ice\Core\Query' => [
                'sql' => 'Ice:File/query',
                'query' => 'Ice:File/query',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File/cache',
            ],
        ]
    ],
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