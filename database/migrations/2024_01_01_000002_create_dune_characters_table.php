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
        Schema::create('dune_rp_characters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('house_id')->nullable();
            $table->text('biography')->nullable();
            $table->string('birthworld')->nullable();
            $table->integer('age')->nullable();
            $table->enum('status', ['alive', 'missing', 'deceased', 'exiled'])->default('alive');
            $table->integer('spice_addiction_level')->default(0);
            $table->json('special_abilities')->nullable();
            $table->string('avatar_url')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('house_id')->references('id')->on('dune_rp_houses')->onDelete('set null');
            $table->index(['user_id', 'is_approved']);
            $table->index(['house_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dune_rp_characters');
    }
};
