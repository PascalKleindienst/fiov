<?php

declare(strict_types=1);

namespace App\Enums;

use function in_array;

enum BudgetType: string
{
    case Default = 'default';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
    case SavingsGoal = 'savings_goal';
    case DebtPayment = 'debt_payment';
    case EmergencyFund = 'emergency_fund';
    case MajorPurchase = 'major_purchase';
    case EventPlanning = 'event_planning';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }

    /**
     * @return BudgetType[]
     */
    public static function recurringStates(): array
    {
        return [
            self::Weekly,
            self::Monthly,
            self::Yearly,
        ];
    }

    /**
     * @return BudgetType[]
     */
    public static function goalBasedStates(): array
    {
        return [
            self::SavingsGoal,
            self::DebtPayment,
            self::EmergencyFund,
            self::MajorPurchase,
            self::EventPlanning,
        ];
    }

    public function isGoalBased(): bool
    {
        return in_array($this, self::goalBasedStates());
    }

    public function isDefault(): bool
    {
        return $this === self::Default;
    }

    public function label(): string
    {
        return match ($this) {
            self::Default => __('budgets.types.default'),
            self::Weekly => __('budgets.types.weekly'),
            self::Monthly => __('budgets.types.monthly'),
            self::Yearly => __('budgets.types.yearly'),
            self::SavingsGoal => __('budgets.types.savings_goal'),
            self::DebtPayment => __('budgets.types.debt_payment'),
            self::EmergencyFund => __('budgets.types.emergency_fund'),
            self::MajorPurchase => __('budgets.types.major_purchase'),
            self::EventPlanning => __('budgets.types.event_planning'),
        };
    }
}
