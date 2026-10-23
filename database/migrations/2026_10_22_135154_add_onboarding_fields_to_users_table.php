<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('ACTIVE')->after('role');
            $table->foreignId('invited_by')->nullable()->after('status')->index();
            $table->timestamp('joined_at')->nullable()->after('invited_by');

            $table->index('status');
        });

        // Normalise legacy direction-based roles into function-based roles.
        DB::table('users')
            ->whereIn('role', ['DONOR_STAFF', 'RECIPIENT_STAFF'])
            ->update(['role' => 'FACILITY_STAFF']);

        DB::table('users')
            ->whereIn('role', ['DONOR_APPROVER', 'RECIPIENT_APPROVER'])
            ->update(['role' => 'FACILITY_APPROVER']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['invited_by']);
            $table->dropColumn(['status', 'invited_by', 'joined_at']);
        });
    }
};
