<?php

declare(strict_types=1);

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Enums\Priority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->index(['user_id', 'type', 'status']);
            $table->index(['start_date', 'end_date']);

            $table->encrypted('title');
            $table->encrypted('description')->nullable();
            $table->json('milestones')->nullable(); // TODO:encrypted?

            $table->string('type')->default(BudgetType::Default);
            $table->string('priority')->default(Priority::Medium);
            $table->string('status')->default(BudgetStatus::Active);

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('budget_category', static function (Blueprint $table): void {
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_category_id')->constrained('wallet_categories')->cascadeOnDelete();
            $table->encrypted('allocated_amount');
            $table->encrypted('used_amount')->nullable();
            $table->string('currency');
            $table->unique(['budget_id', 'wallet_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_category');
        Schema::dropIfExists('budgets');
    }
};
