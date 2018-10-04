<?php

return [
    'image' => 'https://cdn.worldvectorlogo.com/logos/laravel-1.svg',
    'name' => 'Laravel 5',
    'containers' =>
        [
            [
                'id' => 'mysql',
                'container' => 'mysql',
                'config' =>
                    [
                        'General_application' => './docker/database',
                        'General_name' => 'mysql',
                        'Connection_database_user' => 'docker',
                        'Connection_database_password' => 'docker',
                        'Connection_database_name' => 'docker',
                        'General_port' => '3306',
                        'Connection_database_root' => 'yes',
                    ],
            ],
            [
                'id' => 'adminer',
                'container' => 'adminer',
                'config' =>
                    [
                        'General_name' => 'adminer',
                        'General_port' => '8081',
                    ],
            ],
            [
                'id' => 'php',
                'container' => 'php72',
                'config' =>
                    [
                        'General_application' => './',
                        'General_name' => 'php',
                        'Extensions_extensions' => 'pdo_mysql,intl',
                    ],
            ],
            [
                'id' => 'nginx',
                'container' => 'nginx',
                'config' =>
                    [
                        'Nginx_php' => 'php',
                        'General_application' => './',
                        'General_name' => 'nginx',
                        'Nginx_max_upload' => '2M',
                        'Nginx_index' => 'public',
                        'General_port' => '8080',
                    ],
            ]
        ],
];