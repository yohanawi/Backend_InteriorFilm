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
        Schema::table('orders', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 50)->default('pending')->after('status')
                    ->comment('Payment status: pending, processing, completed, failed, cancelled, refunded');
            }
            if (!Schema::hasColumn('orders', 'payment_transaction_id')) {
                $table->string('payment_transaction_id')->nullable()->after('payment_status')
                    ->comment('External payment gateway transaction ID');
            }
            if (!Schema::hasColumn('orders', 'payment_completed_at')) {
                $table->timestamp('payment_completed_at')->nullable()->after('payment_transaction_id')
                    ->comment('When payment was successfully completed');
            }
            if (!Schema::hasColumn('orders', 'payment_error_message')) {
                $table->text('payment_error_message')->nullable()->after('payment_completed_at')
                    ->comment('Error message if payment failed');
            }
            if (!Schema::hasColumn('orders', 'payment_metadata')) {
                $table->json('payment_metadata')->nullable()->after('payment_error_message')
                    ->comment('Additional payment gateway metadata');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_transaction_id',
                'payment_completed_at',
                'payment_error_message',
                'payment_metadata',
            ]);
        });
    }
};
