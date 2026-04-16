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
        Schema::create('villa_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->decimal('price_override', 15, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['villa_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_availabilities');
    }
};
