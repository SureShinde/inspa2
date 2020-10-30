<?php
return [
    'backend' => [
        'frontName' => 'admin_v31w1z'
    ],
    'crypt' => [
        'key' => 'b1dac548cca9add1d68e073552081377'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'inspa',
                'username' => 'rootuser',
                'password' => 'Pucit@1234',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1
    ],
    'install' => [
        'date' => 'Mon, 06 May 2019 13:21:30 +0000'
    ],
    'system' => [
        'default' => [
            'dev' => [
                'debug' => [
                    'debug_logging' => NULL
                ]
            ]
        ]
    ]
];
