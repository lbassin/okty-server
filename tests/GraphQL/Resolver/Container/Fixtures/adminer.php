<?php

return [
    'id' => 'adminer',
    'name' => 'Adminer',
    'docker' => 'adminer',
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