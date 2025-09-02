<?php

return [
    'index' => 'Budget',
    'index_description' => 'Manage your budgets and financial goals',
    'create' => 'Create new budget',
    'create_description' => 'Set up a new budget to track your expenses',
    'edit' => 'Edit budget “:name”',
    'delete' => 'Delete budget “:name”',

    'deleted' => 'Budget “:name” deleted',

    'confirm_delete' => 'Really delete this budget?',
    'confirm_delete_desc' => 'Are you sure you want to delete this budget?',

    'empty' => [
        'title' => 'No budgets found',
        'description' => 'Create a new budget to track your expenses.',
    ],

    'categories' => [
        'title' => 'Categories',
        'select' => 'Select the categories you want to include in this budget and assign amounts to each category.',
        'total_allocated' => 'Total allocated amounts',
        'warning_allocated_amount_exceeded' => 'Warning: The allocated amounts for the categories are too high.',
        'no_categories_found' => 'No categories found.',
    ],

    'types' => [
        'goalBased' => 'Goal-based',
        'recurring' => 'Recurring',
        'default' => 'Default',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'savings_goal' => 'Savings goal',
        'debt_payment' => 'Debt repayment',
        'emergency_fund' => 'Emergency fund',
        'major_purchase' => 'Major purchase',
        'event_planning' => 'Event planning',
    ],

    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'complete' => 'Complete',
        'pause' => 'Pause',
        'resume' => 'Resume',
        'cancel' => 'Cancel',
    ],

    'status' => [
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'paused' => 'Paused',
    ],

    'progress' => [
        'spent' => 'Spent: :amount',
        'remaining' => 'Remaining: :amount',
        'progress' => 'Progress: :amount',
        'completed' => ':percentage% completed',
        'total' => 'Total: :amount',
        'used' => ':percentage% used',
        'target' => 'Target: :amount',
    ],

    'expires' => [
        'today' => 'Today',
        'future' => ':days days remaining',
        'past' => 'Expired :days days ago',
    ],

    'fields' => [
        'name' => 'Name',
        'color' => 'Color',
        'wallet' => 'Wallet',
        'description' => 'Description',
        'type' => 'Type',
        'amount' => 'Amount',
        'currency' => 'Currency',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'is_active' => 'Active',
        'selectedCategories' => 'Categories',
        'allocatedAmounts' => 'Allocated amounts',
        'target_amount' => 'Target amount',
        'current_amount' => 'Current amount',
        'target_date' => 'Target date',
        'priority' => 'Priority',
        'notes' => 'Notes',
        'status' => 'Status',
    ],
];

