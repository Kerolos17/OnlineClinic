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
        Schema::table('slots', function (Blueprint $table) {
            // Add appointment type field
            $table->enum('type', ['online', 'clinic'])->default('clinic')->after('status');

            // Add notes field for clinic-specific instructions
            $table->text('notes')->nullable()->after('type');

            // Add Zoom meeting fields for online appointments
            $table->string('zoom_meeting_id')->nullable()->after('notes');
            $table->text('zoom_join_url')->nullable()->after('zoom_meeting_id');
            $table->text('zoom_start_url')->nullable()->after('zoom_join_url');

            // Add performance indexes
            $table->index('type', 'idx_slots_type');
            $table->index(['doctor_id', 'date', 'type'], 'idx_slots_doctor_date_type');
            $table->index('zoom_meeting_id', 'idx_slots_zoom_meeting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slots', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_slots_type');
            $table->dropIndex('idx_slots_doctor_date_type');
            $table->dropIndex('idx_slots_zoom_meeting');

            // Drop columns
            $table->dropColumn([
                'type',
                'notes',
                'zoom_meeting_id',
                'zoom_join_url',
                'zoom_start_url',
            ]);
        });
    }
};
