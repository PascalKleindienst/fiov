<?php

declare(strict_types=1);

return [
    'index' => 'System',
    'index_description' => 'Anzeigen der Systeminformationen',

    'alerts' => [
        'error' => 'Es gibt Fehler in Ihrer Fiov-Konfiguration. Fiov funktioniert nicht ordnungsgemäß. Weitere Informationen finden Sie unter :path',
        'warning' => 'Ihre Fiov-Konfiguration weist einige Probleme auf. Fiov funktioniert möglicherweise nicht ordnungsgemäß.',
        'success' => 'Ihre Fiov-Installation ist betriebsbereit.'
    ],

    'fields' => [
        'item' => 'Element',
        'value' => 'Wert',
        'status' => 'Status',
    ],

    'license' => [
        'title' => 'Lizenz',
        'licensed_to' => 'Lizenziert an :name <:mail>',
        'key' => 'Lizenzschlüssel: :key',
    ],

    'php_version' => 'PHP Version',
    'npm_version' => 'NPM Version (optional)',
    'node_version' => 'Node Version (optional)',
    'mail_configuration' => 'E-Mail Einstellungen',
    'directory_permission' => ':item Verzeichnis beschreibbar',
];
