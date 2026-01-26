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
        Schema::table('customers', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('customers', 'is_guest')) {
                $table->boolean('is_guest')->default(false)->after('status');
            }

            // Add guest_session_id for tracking guest sessions (optional for analytics)
            if (!Schema::hasColumn('customers', 'guest_session_id')) {
                $table->string('guest_session_id', 100)->nullable()->after('is_guest')->index();
            }

            // Add converted_at timestamp to track when guest becomes registered customer
            if (!Schema::hasColumn('customers', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('guest_session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Only drop columns that exist
            if (Schema::hasColumn('customers', 'converted_at')) {
                $table->dropColumn('converted_at');
            }
            if (Schema::hasColumn('customers', 'guest_session_id')) {
                $table->dropColumn('guest_session_id');
            }
            if (Schema::hasColumn('customers', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
        });
    }
};
