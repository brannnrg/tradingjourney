<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->cascadeOnDelete();
            $table->string('pair'); // BTC/USDT, ETH/USDT, etc.
            $table->enum('direction', ['long', 'short']);
            $table->decimal('quantity', 20, 8); // amount of crypto (e.g. 0.5 BTC)
            $table->decimal('entry_price', 20, 8);
            $table->decimal('exit_price', 20, 8)->nullable();
            $table->decimal('stop_loss', 20, 8)->nullable();
            $table->decimal('take_profit', 20, 8)->nullable();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->decimal('profit_loss', 20, 8)->nullable(); // calculated
            $table->decimal('profit_loss_percent', 10, 4)->nullable(); // calculated
            $table->string('screenshot_path')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
