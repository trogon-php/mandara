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
        Schema::table('client_credentials', function (Blueprint $table) {
            // Increase size for encrypted fields
            $table->text('account_key')->nullable()->change();
            $table->text('account_secret')->nullable()->change();
            $table->text('remarks')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_credentials', function (Blueprint $table) {
            // Revert back to varchar(255)
            $table->string('account_key', 255)->nullable()->change();
            $table->string('account_secret', 255)->nullable()->change();
            $table->string('remarks', 255)->nullable()->change();
        });
    }
};