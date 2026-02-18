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
        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('name')->after('id');
            $table->string('slug')->unique()->after('name');
            $table->string('plan')->default('free')->after('slug');
            $table->timestamp('trial_ends_at')->nullable()->after('plan');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropColumn([
                'name',
                'slug',
                'plan',
                'trial_ends_at',
                'subscription_ends_at',
            ]);
        });
    }
};
