<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('graves', function (Blueprint $table) {
            // Nullable because a grave starts without a headstone
            $table->foreignId('ledger_id')->nullable()->constrained('ledgers', 'ledger_id')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('graves', function (Blueprint $table) {
            $table->dropForeign(['ledger_id']);
            $table->dropColumn('ledger_id');
        });
    }
};