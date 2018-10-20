<?php
return [
    'files' => ['Dockerfile', 'default.conf'],
    'config' => [
        'Dockerfile' => [
            'output' => './'
        ],
        'default.conf' => [
            'output' => './nginx/',
            'args' => [
                'root_folder' => [
                    'default' => ''
                ],
                'max_upload_size' => [
                    'default' => '2M'
                ],
                'php_container_link' => [
                    'default' => 'php'
                ]
            ]
        ]
    ]
];
