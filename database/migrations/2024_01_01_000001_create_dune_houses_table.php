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
        Schema::create('dune_rp_houses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('sigil_url')->nullable();
            $table->string('motto')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('leader_id')->nullable();
            $table->string('homeworld')->nullable();
            $table->string('color', 7)->default('#8B4513'); // Hex color
            $table->decimal('spice_reserves', 15, 2)->default(0);
            $table->integer('influence_points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('leader_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['is_active', 'influence_points']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dune_rp_houses');
    }
};
