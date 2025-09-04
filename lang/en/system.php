<?php

declare(strict_types=1);

return [
    'index' => 'System',
    'index_description' => 'Display system information',

    'alerts' => [
        'error' => 'There are errors in your Fiov setup. Fiov will not work properly. You can find more details in :path',
        'warning' => 'Your Fiov setup has some issues. Fiov might not work properly',
        'success' => 'Your Fiov installation is good to go.'
    ],

    'fields' => [
        'item' => 'Item',
        'value' => 'Value',
        'status' => 'Status',
    ],

    'php_version' => 'PHP Version',
    'npm_version' => 'NPM Version (optional)',
    'node_version' => 'Node Version (optional)',
    'mail_configuration' => 'Mail Configuration',
    'directory_permission' => ':item folder writable',
];
