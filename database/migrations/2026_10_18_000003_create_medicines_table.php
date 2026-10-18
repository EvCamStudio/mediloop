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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name');
            $table->string('sku')->unique();
            $table->string('category'); // Antibiotic, Cardiovascular, Endocrine, Analgesic, Emergency
            $table->string('dosage_form'); // Vial Injeksi, Tablet, Sirup, Infus
            $table->string('unit'); // Vial, Box, Strip, Botol, Kolf
            $table->decimal('unit_price_idr', 12, 2)->default(0);
            $table->boolean('requires_cold_chain')->default(false);
            $table->decimal('storage_min_temp', 4, 1)->nullable();
            $table->decimal('storage_max_temp', 4, 1)->nullable();
            $table->text('handling_instructions')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('generic_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
