<?php

return [
    'ice_main' => [
        'route' => '/',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Hello world']],
                        'Ice:Main' => 'main'
                    ]
                ]
            ]
        ]
    ],
    'ice_test' => [
        'route' => '/test',
        'request' => [
            'GET' => [
                'Ice:Layout_Test' => [
                    'actions' => [
                        'Ice:Test' => 'testAction'
                    ]
                ]
            ]
        ]
    ],
    'ice_ajax' => [
        'route' => '/ajax'
    ],
    'ice_widget_form_file_upload' => [
        'route' => '/widget/form/file/upload',
        'request' => ['POST' => 'Ice:Widget_Form_File_Upload']
    ],
    'ice_locale' => [
        'route' => '/locale/{$locale}',
        'params' => [
            'locale' => '([a-z]+)',
        ],
        'request' => [
            'GET' => 'Ice:Locale'
        ]
    ],
    'ice_redirect' => [
        'route' => '/redirect',
        'request' => [
            'GET' => [
                'Ice:Layout_Blank' => [
                    'response' => [
                        'redirect' => 'ice_main'
                    ]
                ]
            ]
        ]
    ],
    'ice_render_excel' => [
        'route' => '/render/excel',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render_Excel',
            ],
            'POST' => [
                'actionClass' => 'Ice:Render_Excel',
            ]
        ]
    ],
    '_Security' => '/security',
    '_Private' => '/private',
    '_CKEditor' => '/ckeditor',
];