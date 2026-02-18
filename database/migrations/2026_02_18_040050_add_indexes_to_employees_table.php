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
        Schema::table('employees', function (Blueprint $table) {
            $table->index('name');
            $table->index('email');
            $table->index('department');
            $table->index('position');
            $table->index('status');
            $table->index('salary');
            $table->index('start_date');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['email']);
            $table->dropIndex(['department']);
            $table->dropIndex(['position']);
            $table->dropIndex(['status']);
            $table->dropIndex(['salary']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
