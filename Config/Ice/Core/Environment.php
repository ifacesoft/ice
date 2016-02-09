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
        'Ice\Core\DataProvider' => [
            'Ice\DataProvider\Redis' => [
                'session' => [
                    'ttl' => 60 * 60 * 24
                ],
            ],
            'Ice\DataProvider\File' => [
                'session' => [
                    'ttl' => 60 * 60 * 24
                ],
            ],
            'Ice\DataProvider\Mysqli' => [
                'default' => [
                    'username' => 'root',
                    'password' => '',
                ]
            ],
            'Ice\DataProvider\Mongodb' => [
                'default' => [
                ]
            ]
        ],
        'dataProviderKeys' => [
            'Ice\Core\Bootstrap' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Security' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\MessageTransport' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:Repository',
                'cache' => 'Ice:Repository'
            ],
            'Ice\Core\Router' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:Repository',
                'route' => 'Ice:Repository',
                'routes' => 'Ice:Repository',
            ],
            'Ice\Core\Config' => [
                'instance' => 'Ice:Repository',
                'config' => 'Ice:Repository',
                'route' => 'Ice:Repository',
                'routes' => 'Ice:Repository',
                'model_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\Converter' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Widget' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Widget_Scope' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Widget\Model_Form' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Widget_Menu' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Widget\Model_Table' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Render' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\QueryTranslator' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Query_Scope' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:Repository',
                'sql' => 'Ice:Repository',
                'query' => 'Ice:Repository',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\DataSource' => [
                'instance' => 'Ice:Repository',
                'cache' => 'Ice:File'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:Repository',
            ],
            'Ice\Core\ViiewOld' => [
                'instance' => 'Ice:Repository',
                'view' => 'Ice:Repository',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:Repository',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:Repository'
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:Repository'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
            'Ice\Core\SessionHandler' => [
                'instance' => 'Ice:Repository'
            ]

        ],
    ],
    'test' => [
        'dataProviderKeys' => [
            'Ice\Core\Bootstrap' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\MessageTransport' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:File',
                'cache' => 'Ice:File'
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:File',
                'route' => 'Ice:File',
                'routes' => 'Ice:File',
            ],
            'Ice\Core\Config' => [
                'instance' => 'Ice:File',
                'config' => 'Ice:File',
                'route' => 'Ice:File',
                'routes' => 'Ice:File',
                'model_scheme' => 'Ice:File',
            ],
            'Ice\Core\Converter' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Widget' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Widget_Scope' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Widget\Model_Form' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Widget_Menu' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Widget\Model_Table' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Render' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\QueryTranslator' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Query_Scope' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:File',
                'sql' => 'Ice:File',
                'query' => 'Ice:File',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\DataSource' => [
                'instance' => 'Ice:File',
                'cache' => 'Ice:File'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:File',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:File',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File',
            ],
            'Ice\Core\ViiewOld' => [
                'instance' => 'Ice:File',
                'view' => 'Ice:File',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:File',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:File'
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:File'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
            'Ice\Core\SessionHandler' => [
                'instance' => 'Ice:Repository'
            ]
        ]
    ],
    'development' => [
        'dataProviderKeys' => [
            'Ice\Core\Bootstrap' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\MessageTransport' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry',
            ],
            'Ice\Core\Action' => [
                'instance' => 'Ice:Registry',
                'output' => 'Ice:Registry'
            ],
            'Ice\Core\Route' => [
                'instance' => 'Ice:Registry',
                'route' => 'Ice:Registry',
                'routes' => 'Ice:Registry',
            ],
            'Ice\Core\Config' => [
                'instance' => 'Ice:Registry',
                'config' => 'Ice:Registry',
                'route' => 'Ice:Registry',
                'routes' => 'Ice:Registry',
                'model_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\Converter' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Widget' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Widget_Scope' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Widget\Model_Form' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Widget_Menu' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Widget\Model_Table' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Container' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Render' => [
                'instance' => 'Ice:Registry'
            ],
            'Ice\Core\QueryTranslator' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Query_Scope' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Query' => [
                'instance' => 'Ice:Registry',
                'sql' => 'Ice:Registry',
                'query' => 'Ice:Registry',
            ],
            'Ice\Core\Code_Generator' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\DataSource' => [
                'instance' => 'Ice:Registry',
                'cache' => 'Ice:Registry'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\Validator' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File',
            ],
            'Ice\Core\ViiewOld' => [
                'instance' => 'Ice:Registry',
                'view' => 'Ice:Registry',
            ],
            'Ice\Core\View' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Resource' => [
                'instance' => 'Ice:Registry',
            ],
            'Ice\Core\Module' => [
                'instance' => 'Ice:Registry'
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
            'Ice\Core\SessionHandler' => [
                'instance' => 'Ice:Registry'
            ]
        ],
    ]
];