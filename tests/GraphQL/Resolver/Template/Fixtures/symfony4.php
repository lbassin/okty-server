<?php

return [
    'image' => 'https://cdn.worldvectorlogo.com/logos/symfony.svg',
    'name' => 'Symfony 4',
    'containers' =>
        [
            [
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
                'configPath' => 'mysql',
                'containerId' => 'mysql',
            ],
            [
                'config' =>
                    [
                        'General_name' => 'adminer',
                        'General_port' => '8081',
                    ],
                'configPath' => 'adminer',
                'containerId' => 'adminer',
            ],
            [
                'config' =>
                    [
                        'General_application' => './',
                        'General_name' => 'php',
                        'Extensions_extensions' => 'pdo_mysql,intl,opcache,apcu,xdebug',
                    ],
                'configPath' => 'php72',
                'containerId' => 'php',
            ],
            [
                'config' =>
                    [
                        'Nginx_php' => 'php',
                        'General_application' => './',
                        'General_name' => 'nginx',
                        'Nginx_max_upload' => '2M',
                        'Nginx_index' => 'public',
                        'General_port' => '8080',
                    ],
                'configPath' => 'nginx',
                'containerId' => 'nginx',
            ],
        ],
];