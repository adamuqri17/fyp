<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ledger_orders', function (Blueprint $table) {
        $table->id('order_id'); // Matches proposal
        $table->foreignId('grave_id')->constrained('graves', 'grave_id')->onDelete('cascade');
        $table->foreignId('ledger_id')->constrained('ledgers', 'ledger_id');
        
        $table->string('buyer_name');
        $table->string('buyer_phone');
        $table->dateTime('transaction_date');
        $table->decimal('amount', 10, 2);
        $table->enum('status', ['Pending', 'Paid', 'Installed', 'Cancelled'])->default('Pending');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_orders');
    }
};
