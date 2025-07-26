<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_categories', function (Blueprint $table): void {
            $table->id();
            $table->text('title');  // encrypted
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->foreignIdFor(User::class)->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_categories');
    }
};
