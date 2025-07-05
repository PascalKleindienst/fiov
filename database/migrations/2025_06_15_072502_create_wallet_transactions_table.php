<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Models\WalletCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('transaction_id')->unique();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->integer('amount');
            $table->string('currency')->nullable();
            $table->boolean('is_investment');
            $table->foreignIdFor(Wallet::class)->constrained('wallets')->onDelete('cascade');
            $table->foreignIdFor(WalletCategory::class)->constrained('wallet_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
