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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            $table->string('role')->default('DONOR_STAFF'); // DONOR_STAFF, DONOR_APPROVER, RECIPIENT_STAFF, RECIPIENT_APPROVER, DINKES_AUDITOR, SYSADMIN
            $table->string('license_sip')->nullable(); // Surat Izin Praktik Apoteker
            $table->string('job_title')->nullable();

            $table->index(['facility_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->dropColumn(['facility_id', 'role', 'license_sip', 'job_title']);
        });
    }
};
