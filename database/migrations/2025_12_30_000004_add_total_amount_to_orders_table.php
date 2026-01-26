<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add total_amount column only if it doesn't exist
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->after('total')->nullable();
            }

            // Add indexes for better query performance
            if (!Schema::hasIndex('orders', 'orders_paid_at_index')) {
                $table->index('paid_at');
            }
            if (!Schema::hasIndex('orders', 'orders_shipped_at_index')) {
                $table->index('shipped_at');
            }
            if (!Schema::hasIndex('orders', 'orders_delivered_at_index')) {
                $table->index('delivered_at');
            }
            if (!Schema::hasIndex('orders', 'orders_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!Schema::hasIndex('orders', 'orders_payment_status_created_at_index')) {
                $table->index(['payment_status', 'created_at']);
            }
        });

        // Copy total to total_amount for existing records where total_amount is null
        DB::statement('UPDATE orders SET total_amount = total WHERE total_amount IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['orders_paid_at_index']);
            $table->dropIndex(['orders_shipped_at_index']);
            $table->dropIndex(['orders_delivered_at_index']);
            $table->dropIndex(['orders_status_created_at_index']);
            $table->dropIndex(['orders_payment_status_created_at_index']);
            $table->dropColumn('total_amount');
        });
    }
};
