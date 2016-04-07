<?php
return [
    'ice_ckeditor_browse' => [
        'route' => '/browse',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Vendor_CKEditor_Browse',
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_ckeditor_delete' => [
        'route' => '/delete',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Vendor_CKEditor_Delete',
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_ckeditor_upload' => [
        'route' => '/upload',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Vendor_CKEditor_Upload',
            ]
        ],
        'parent' => 'ice_security'
    ],
];