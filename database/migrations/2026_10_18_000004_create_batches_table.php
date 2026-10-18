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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->string('batch_number');
            $table->integer('current_stock');
            $table->decimal('daily_burn_rate', 8, 2)->default(0); // Units dispensed per day
            $table->date('expiry_date');
            $table->decimal('unit_price_idr', 12, 2)->default(0);
            $table->string('storage_location')->nullable(); // e.g. Rak B-04 / Cold Room 2
            $table->string('status')->default('ACTIVE'); // ACTIVE, RESERVED, DEPLETED, EXPIRED
            $table->timestamps();

            $table->unique(['facility_id', 'medicine_id', 'batch_number']);
            $table->index(['facility_id', 'expiry_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
