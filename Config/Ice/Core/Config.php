<?php
/**
 * @file Ice main config
 *
 * Sets default config params for ice application components
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */

use Ice\Core\Data;
use Ice\Helper\Date;

return [
    'Ice\Core\Model' => [
        'prefixes' => [
            'ice' => 'Ice',
        ]
    ],
    'Ice\Helper\Api_Client_Yandex_Translate' => [
        'translateKey' => 'trnsl.1.1.20150207T134028Z.19bab9f8d9228706.89067e4f90535d4a934a39fbaf284d8af968c9a9'
    ],
    'Ice\Core\Data_Source' => [
        'Ice\Data\Source\Mysqli' => [
            'default' => 'test',
        ],
        'Ice\Data\Source\Mongodb' => [
            'default' => 'test',
        ],
    ],
    'Ice\Core\Environment' => [
        'environments' => [
            '/localhost/' => 'development',
            '/.*/' => 'production'
        ]
    ],
    'Ice\View\Render\Cli' => [],
    'Ice\View\Render\Json' => [],
    'Ice\View\Render\Php' => [],
    'Ice\View\Render\Replace' => [],
    'Ice\View\Render\Smarty' => [
        'dataProviderKey' => 'Ice:Registry/view_render',
        'vendor' => 'smarty/smarty',
        'templates_c' => 'smarty/templates_c/',
        'plugins' => [ICE_DIR . 'Vendor/Smarty/plugins']
    ],
    'Ice\View\Render\Twig' => [
        'vendor' => 'twig/twig',
        'cache' => 'twig/cache/',
    ],
    'Ice\Core\View' => [
        'layout' => null,
        'defaultViewRenderClassName' => 'Ice:Php'
    ],
    'Ice\Core\Request' => [
        'multiLocale' => 1,
        'locale' => 'en',
        'cors' => [
//            'host' => [
//                'methods' => [], // Permitted types of request ('POST', 'OPTIONS')
//                'headers' => [], // Describe custom headers ('Origin', 'X-Requested-With', 'Content-Range', 'Content-Disposition', 'Content-Type')
//                'cookie' => 'true' // Allow cookie
//            ]
        ]
    ],
    'defaults' => [
        'Ice\Core\Route' => [
            'params' => [],
            'weight' => 0,
            'request' => [
                'GET' => [
                    'layout' => 'Ice:Layout_Main'
                ]
            ]
        ],
        'Ice\Core\Model_Scheme' => [
            'time' => Date::get(),
            'revision' => date('00000000'),
            'tableName' => null,
            'dataSourceKey' => null,
            'fields' => [],
        ]
    ]
];