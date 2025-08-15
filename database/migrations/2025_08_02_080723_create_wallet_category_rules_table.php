<?php

declare(strict_types=1);

use App\Models\WalletCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_category_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('field')->default('title');
            $table->string('operator');
            $table->text('value');
            $table->foreignIdFor(WalletCategory::class)->constrained('wallet_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_category_rules');
    }
};
