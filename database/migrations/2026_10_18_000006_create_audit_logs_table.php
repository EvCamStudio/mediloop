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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            $table->foreignId('transfer_id')->nullable()->constrained('transfers')->cascadeOnDelete();
            $table->string('action'); // TRANSFER_PROPOSED, TRANSFER_APPROVED, TRANSFER_ACCEPTED, IN_TRANSIT, TRANSFER_COMPLETED, TRANSFER_CANCELLED
            $table->string('ip_address')->nullable();
            $table->json('state_delta')->nullable();
            $table->string('description');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['facility_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
