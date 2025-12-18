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
        // Doctors table indexes
        Schema::table('doctors', function (Blueprint $table) {
            $table->index('specialization_id', 'idx_doctors_specialization');
            $table->index('is_active', 'idx_doctors_active');
            $table->index(['is_active', 'rating'], 'idx_doctors_active_rating');
            $table->index(['specialization_id', 'is_active'], 'idx_doctors_spec_active');
        });

        // Slots table indexes
        Schema::table('slots', function (Blueprint $table) {
            $table->index('doctor_id', 'idx_slots_doctor');
            $table->index('date', 'idx_slots_date');
            $table->index('status', 'idx_slots_status');
            $table->index(['doctor_id', 'date', 'status'], 'idx_slots_doctor_date_status');
        });

        // Bookings table indexes
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('doctor_id', 'idx_bookings_doctor');
            $table->index('slot_id', 'idx_bookings_slot');
            $table->index('status', 'idx_bookings_status');
            $table->index('appointment_at', 'idx_bookings_appointment');
            $table->index(['doctor_id', 'status'], 'idx_bookings_doctor_status');
            $table->index('patient_email', 'idx_bookings_email');
        });

        // Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('doctor_id', 'idx_reviews_doctor');
            $table->index('status', 'idx_reviews_status');
            $table->index(['doctor_id', 'status'], 'idx_reviews_doctor_status');
        });

        // Specializations table indexes
        Schema::table('specializations', function (Blueprint $table) {
            $table->index('is_active', 'idx_specializations_active');
        });

        // Users table indexes (if not already exists)
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email_index')) {
                $table->index('email', 'idx_users_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex('idx_doctors_specialization');
            $table->dropIndex('idx_doctors_active');
            $table->dropIndex('idx_doctors_active_rating');
            $table->dropIndex('idx_doctors_spec_active');
        });

        // Slots table
        Schema::table('slots', function (Blueprint $table) {
            $table->dropIndex('idx_slots_doctor');
            $table->dropIndex('idx_slots_date');
            $table->dropIndex('idx_slots_status');
            $table->dropIndex('idx_slots_doctor_date_status');
        });

        // Bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_doctor');
            $table->dropIndex('idx_bookings_slot');
            $table->dropIndex('idx_bookings_status');
            $table->dropIndex('idx_bookings_appointment');
            $table->dropIndex('idx_bookings_doctor_status');
            $table->dropIndex('idx_bookings_email');
        });

        // Reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_doctor');
            $table->dropIndex('idx_reviews_status');
            $table->dropIndex('idx_reviews_doctor_status');
        });

        // Specializations table
        Schema::table('specializations', function (Blueprint $table) {
            $table->dropIndex('idx_specializations_active');
        });

        // Users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'idx_users_email')) {
                $table->dropIndex('idx_users_email');
            }
        });
    }
};
