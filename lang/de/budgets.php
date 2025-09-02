<?php

return [
    'index' => 'Budget',
    'index_description' => 'Verwalten Sie Ihre Budgets und finanzielle Ziele',
    'create' => 'Neues Budget erstellen',
    'create_description' => 'Richten Sie ein neues Budget ein, um Ihre Ausgaben zu verfolgen',
    'edit' => 'Budget ":name" bearbeiten',
    'delete' => 'Budget ":name" löschen',

    'deleted' => 'Budget ":name" gelöscht',

    'confirm_delete' => 'Dieses Budget wirklich löschen?',
    'confirm_delete_desc' => 'Sind Sie sicher, dass Sie dieses Budget löschen wollen?',

    'empty' => [
        'title' => 'Keine Budgets gefunden',
        'description' => 'Erstellen Sie ein neues Budget, um Ihre Ausgaben zu verfolgen.',
    ],

    'categories' => [
        'title' => 'Kategorien',
        'select' => 'Wählen Sie die Kategorien aus, die in diesem Budget enthalten sein sollen, und weisen Sie jeder Kategorie Beträge zu.',
        'total_allocated' => 'Gesamt zugeordnete Beträge',
        'warning_allocated_amount_exceeded' => 'Warnung: Die zugeordneten Beträge der Kategorien sind zu hoch.',
        'no_categories_found' => 'Keine Kategorien gefunden.',
    ],

    'types' => [
        'goalBased' => 'Zielbasiert',
        'recurring' => 'Wiederkehrend',
        'default' => 'Standard',
        'weekly' => 'Wöchentlich',
        'monthly' => 'Monatlich',
        'yearly' => 'Jährlich',
        'savings_goal' => 'Einnahmenziel',
        'debt_payment' => 'Schuldenrückzahlung',
        'emergency_fund' => 'Notfallfond',
        'major_purchase' => 'Großanschaffung',
        'event_planning' => 'Eventplanung',
    ],

    'actions' => [
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'complete' => 'Abschließen',
        'pause' => 'Pausieren',
        'resume' => 'Fortsetzen',
        'cancel' => 'Abbrechen',
    ],

    'status' => [
        'active' => 'Aktiv',
        'completed' => 'Abgeschlossen',
        'cancelled' => 'Abgebrochen',
        'paused' => 'Pausiert',
    ],

    'progress' => [
        'spent' => 'Ausgegeben: :amount',
        'remaining' => 'Verbleibend: :amount',
        'progress' => 'Fortschritt: :amount',
        'completed' => ':percentage% erreicht',
        'total' => 'Gesamt: :amount',
        'used' => ':percentage% benutzt',
        'target' => 'Ziel: :amount',
    ],

    'expires' => [
        'today' => 'Heute',
        'future' => ':days Tage verbleiben',
        'past' => 'Vor :days Tagen abgelaufen',
    ],

    'fields' => [
        'name' => 'Name',
        'color' => 'Farbe',
        'wallet' => 'Konto',
        'description' => 'Beschreibung',
        'type' => 'Typ',
        'amount' => 'Betrag',
        'currency' => 'Währung',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'is_active' => 'Aktiv',
        'selectedCategories' => 'Kategorien',
        'allocatedAmounts' => 'Zugeordnete Beträge',
        'target_amount' => 'Zielbetrag',
        'current_amount' => 'Aktueller Betrag',
        'target_date' => 'Zieltermin',
        'priority' => 'Priorität',
        'notes' => 'Notizen',
        'status' => 'Status',
    ],
];
