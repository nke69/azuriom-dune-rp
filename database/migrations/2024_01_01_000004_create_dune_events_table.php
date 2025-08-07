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
        Schema::create('dune_rp_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('organizer_id');
            $table->unsignedBigInteger('organizer_house_id')->nullable();
            $table->timestamp('event_date');
            $table->string('location')->nullable();
            $table->integer('max_participants')->nullable();
            $table->decimal('spice_cost', 10, 2)->default(0);
            $table->decimal('reward_spice', 10, 2)->default(0);
            $table->enum('event_type', ['harvest', 'combat', 'negotiation', 'ceremony', 'exploration', 'trade', 'council']);
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organizer_house_id')->references('id')->on('dune_rp_houses')->onDelete('set null');
            $table->index(['event_date', 'status']);
            $table->index(['organizer_house_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dune_rp_events');
    }
};
