<?php

return [
    'index' => 'Categories',
    'create' => 'Create category',
    'edit' => 'Edit category ":name"',
    'delete' => 'Delete category ":name"',
    'deleted' => 'Category ":name" deleted',
    'empty' => 'No categories',

    'fields' => [
        'title' => 'Title',
        'color' => 'Color',
        'icon' => 'Icon',
        'rules' => [
            'title' => 'Rules',
            'operator' => 'Operator',
            'field' => 'Field',
            'value' => 'Value',
        ]
    ],

    'rules' => [
        'add' => 'Add rule',
        'remove' => 'Remove rule',
    ],

    'confirm_delete' => 'Are you sure you want to delete this category?',
    'confirm_delete_desc' => 'Are you sure you want to delete this category?',
];
