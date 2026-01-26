<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('ngenius_reference')->nullable()->after('payment_method');
            $table->text('ngenius_payment_url')->nullable()->after('ngenius_reference');
            $table->string('ngenius_currency', 8)->nullable()->after('ngenius_payment_url');
            $table->integer('ngenius_amount_minor')->nullable()->after('ngenius_currency');
            $table->string('ngenius_last_payment_state')->nullable()->after('ngenius_amount_minor');
        });

        // Allow the new payment method in the enum.
        // MySQL (Laragon default) requires altering the ENUM definition.
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE `orders` MODIFY `payment_method` ENUM('credit_card','debit_card','paypal','stripe','cash_on_delivery','bank_transfer','ngenius_hpp') NOT NULL DEFAULT 'cash_on_delivery'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum change first (MySQL)
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE `orders` MODIFY `payment_method` ENUM('credit_card','debit_card','paypal','stripe','cash_on_delivery','bank_transfer') NOT NULL DEFAULT 'cash_on_delivery'"
            );
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'ngenius_reference',
                'ngenius_payment_url',
                'ngenius_currency',
                'ngenius_amount_minor',
                'ngenius_last_payment_state',
            ]);
        });
    }
};
