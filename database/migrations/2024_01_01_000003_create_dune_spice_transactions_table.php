<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dune_rp_spice_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_id');
            $table->enum('type', ['income', 'expense', 'transfer', 'tribute', 'trade']);
            $table->decimal('amount', 15, 2);
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('related_event_id')->nullable();
            $table->timestamps();

            $table->foreign('house_id')->references('id')->on('dune_rp_houses')->onDelete('cascade');
            $table->index(['house_id', 'type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dune_rp_spice_transactions');
    }
};
