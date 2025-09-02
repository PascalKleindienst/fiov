<?php

declare(strict_types=1);

use App\Enums\BudgetType;

it('returns the recurring states', function (): void {
    expect(BudgetType::recurringStates())
        ->toBeArray()
        ->toContain(
            BudgetType::Weekly,
            BudgetType::Monthly,
            BudgetType::Yearly,
        );
});

it('returns the goal based states', function (): void {
    expect(BudgetType::goalBasedStates())
        ->toBeArray()
        ->toContain(
            BudgetType::SavingsGoal,
            BudgetType::DebtPayment,
            BudgetType::EmergencyFund,
            BudgetType::MajorPurchase,
            BudgetType::EventPlanning,
        );
});

it('checks if the state is goal based', function (BudgetType $type, $expected): void {
    expect($type->isGoalBased())->toBe($expected);
})->with([
    [BudgetType::Default, false],
    [BudgetType::SavingsGoal, true],
]);

it('checks if the state is the default state', function (BudgetType $type, $expected): void {
    expect($type->isDefault())->toBe($expected);
})->with([
    [BudgetType::Default, true],
    [BudgetType::SavingsGoal, false],
]);

it('returns the values', function(): void {
    expect(BudgetType::values())
        ->toBeArray()
        ->toContain(
            'default', 'weekly', 'monthly', 'yearly', 'savings_goal', 'debt_payment', 'emergency_fund', 'major_purchase', 'event_planning',
        );
});
