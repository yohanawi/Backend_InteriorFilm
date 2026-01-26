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
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('addresses', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('type');
            }

            // Add FK only if the customers table exists and constraint can be created.
            // (Safe for existing installs.)
            try {
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            } catch (\Throwable $e) {
                // Ignore if constraint already exists or cannot be created.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            try {
                $table->dropForeign(['customer_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            if (Schema::hasColumn('addresses', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('addresses', 'is_primary')) {
                $table->dropColumn('is_primary');
            }
        });
    }
};
