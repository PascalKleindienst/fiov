<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('users', static function (Blueprint $table): void {
                $table->binary('encrypted_dek')->after('password'); // Encrypted DEK
                $table->string('encryption_salt')->after('encrypted_dek'); // Base64-encoded salt
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->dropColumn('encrypted_dek');
            $table->dropColumn('encryption_salt');
        });
    }
};
