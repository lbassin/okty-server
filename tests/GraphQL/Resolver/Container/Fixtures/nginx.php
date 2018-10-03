<?php

return [
    'id' => 'nginx',
    'name' => 'Nginx',
    'docker' => 'nginx',
    'config' => [
        'id' => 'general',
        'label' => 'General',
        'fields' => [
            'id' => 'name',
            'label' => 'Container ID',
            'validators' => [['name' => 'required', 'value' => true]]
        ]
    ]
];