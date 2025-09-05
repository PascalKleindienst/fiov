<?php

declare(strict_types=1);

return [
    'index' => 'Benutzerverwaltung',
    'index_description' => 'Verwaltung der Benutzerkonten',

    'confirm_delete' => 'Diesen Benutzer wirklich löschen?',
    'confirm_delete_desc' => 'Sind Sie sicher, dass Sie diesen Benutzer löschen wollen?',

    'actions' => [
        'edit' => 'Bearbeiten',
        'invite' => 'Einladen',
        'delete' => 'Löschen',
    ],

    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'E-Mail',
        'level' => 'Level',
    ],

    'invite' => [
        'success_info' => 'Eine Einladung mit Benuzterlevel :level wurde an :mail gesendet.',
        'subject' => 'Du wurdest zu Fiov eingeladen!',
        'greeting' => 'Hallo,',
        'body' => 'Du wurdest von :name zu :app eingeladen!',
        'action' => 'Registriere dich jetzt!',
        'note' => 'Notiz: Diese Link läuft nach 24 Stunden ab!',
    ],

    'update' => [
        'title' => 'Benutzer aktualisieren',
    ],
];
