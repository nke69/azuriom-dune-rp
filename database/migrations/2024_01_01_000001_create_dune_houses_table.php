<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dune_rp_houses', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->unsignedInteger('user_id'); // users.id = INT UNSIGNED
            $table->string('name')->unique();
            $table->string('sigil_url')->nullable();
            $table->string('motto')->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('leader_id')->nullable(); // users.id
            $table->foreign('leader_id')->references('id')->on('users')->nullOnDelete();

            $table->string('homeworld')->nullable();
            $table->string('color', 7)->default('#8B4513');
            $table->decimal('spice_reserves', 15, 2)->default(0);
            $table->integer('influence_points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dune_rp_houses');
    }
};
