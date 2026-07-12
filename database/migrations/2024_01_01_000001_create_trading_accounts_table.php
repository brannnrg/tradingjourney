<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_name');
            $table->string('exchange')->nullable(); // Binance, OKX, Bybit, etc.
            $table->string('currency')->default('USDT');
            $table->decimal('initial_balance', 20, 8)->default(100);
            $table->date('start_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_accounts');
    }
};
