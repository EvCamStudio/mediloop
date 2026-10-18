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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. FSK-RSUD-TRK
            $table->string('type'); // RSUD, PUSKESMAS, KLINIK, DINKES
            $table->string('license_number')->nullable();
            $table->string('address');
            $table->string('city')->default('Jakarta Pusat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('cold_chain_ready')->default(true);
            $table->string('contact_person');
            $table->string('phone');
            $table->timestamps();

            $table->index('type');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
