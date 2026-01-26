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
            // Add card information fields
            $table->string('card_scheme', 50)->nullable()->after('ngenius_last_payment_state')
                ->comment('Card brand/scheme (VISA, MASTERCARD, etc.)');

            $table->string('card_last4', 4)->nullable()->after('card_scheme')
                ->comment('Last 4 digits of card');

            $table->string('transaction_id')->nullable()->after('card_last4')
                ->comment('Payment gateway transaction ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['card_scheme', 'card_last4', 'transaction_id']);
        });
    }
};
