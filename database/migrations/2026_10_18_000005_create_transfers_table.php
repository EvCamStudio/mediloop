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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_code')->unique(); // e.g. TRF-20261018-001
            $table->foreignId('donor_facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('recipient_facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->integer('quantity');

            // Transfer State Machine
            $table->string('status')->default('PROPOSED'); // PROPOSED, APPROVED, ACCEPTED, IN_TRANSIT, COMPLETED, CANCELLED, REJECTED

            // Algorithmic Decision-Support Metrics
            $table->decimal('need_score', 5, 2)->default(0); // 50%
            $table->decimal('proximity_score', 5, 2)->default(0); // 30%
            $table->decimal('logistics_score', 5, 2)->default(0); // 20%
            $table->decimal('match_score', 5, 2)->default(0);
            $table->decimal('distance_km', 8, 2)->default(0);
            $table->integer('recipient_daily_burn_rate')->default(0);
            $table->integer('recipient_intake_cap')->default(0);

            // Logistics & Verification
            $table->string('tracking_number')->nullable();
            $table->string('courier_type')->nullable(); // Dedicated Health Courier, Cold-Chain Box, Dinas Pick-up
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Actors & Lifecycle Timestamps
            $table->foreignId('proposed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('proposed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'donor_facility_id']);
            $table->index(['status', 'recipient_facility_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
