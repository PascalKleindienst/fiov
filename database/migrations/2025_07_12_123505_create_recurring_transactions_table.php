<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transactions', static function (Blueprint $table): void {
            $table->id();
            $table->text('title');  // encrypted
            $table->string('icon')->nullable();
            $table->text('amount'); // encrypted
            $table->string('currency');
            $table->boolean('is_investment')->default(false);
            $table->string('frequency');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->dateTime('last_processed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignIdFor(User::class)->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(Wallet::class)->constrained('wallets')->cascadeOnDelete();
            $table->foreignIdFor(WalletCategory::class)->constrained('wallet_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
