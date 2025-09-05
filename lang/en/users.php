<?php

declare(strict_types=1);

return [
    'index' => 'User Management',
    'index_description' => 'User account management',

    'confirm_delete' => 'Are you sure you want to delete this user?',
    'confirm_delete_desc' => 'Are you sure you want to delete this user?',

    'actions' => [
        'edit' => 'Edit',
        'invite' => 'Invite',
        'delete' => 'Delete',
    ],

    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'level' => 'Level',
    ],

    'invite' => [
        'success_info' => 'An invitation with user level :level has been sent to :mail.',
        'subject' => 'You have been invited to Fiov!',
        'greeting' => 'Hello,',
        'body' => 'You have been invited to :app by :name!',
        'action' => 'Register now!',
        'note' => 'Note: This link will expire after 24 hours!',
    ],

    'update' => [
        'title' => 'Update user',
    ],
];
