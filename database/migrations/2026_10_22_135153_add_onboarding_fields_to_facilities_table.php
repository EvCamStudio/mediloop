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
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('ownership')->nullable()->after('type');
            $table->string('province')->nullable()->after('city');
            $table->string('district')->nullable()->after('province');
            $table->string('email')->nullable()->after('phone');
            $table->string('status')->default('PENDING')->after('cold_chain_ready');
            $table->timestamp('verified_at')->nullable()->after('status');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable()->after('verified_by');

            $table->index('status');
        });

        // Facilities created before onboarding existed are treated as verified.
        DB::table('facilities')->update([
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'ownership',
                'province',
                'district',
                'email',
                'status',
                'verified_at',
                'verified_by',
                'notes',
            ]);
        });
    }
};
