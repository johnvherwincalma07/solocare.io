<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solo_parent_applications', function (Blueprint $table) {
            $table->id();

            // 🔗 User relationship (optional but recommended)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 🔢 Unique Reference Number
            $table->string('reference_no')->unique();

            // 🧍 Applicant Information
            $table->string('full_name');
            $table->enum('sex', ['Male', 'Female', 'Other']);
            $table->integer('age');
            $table->string('place_of_birth');
            $table->date('birth_date');
            $table->string('address', 255);
            $table->string('educational_attainment')->nullable();
            $table->enum('civil_status', ['Single', 'Married', 'Separated', 'Widowed']);
            $table->string('occupation')->nullable();
            $table->string('religion')->nullable();
            $table->string('company_agency')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->enum('employment_status', ['Employed', 'Unemployed', 'Self-Employed'])->nullable();
            $table->string('contact_number', 20);
            $table->string('email', 100)->nullable();
            $table->string('pantawid')->nullable();
            $table->string('indigenous_person')->nullable();
            $table->string('lgbtq')->nullable();
            $table->string('pwd')->nullable();

            // 👨‍👩‍👧 Family Details (JSON)
            $table->json('family')->nullable();

            // 📋 Application Details
            $table->text('solo_parent_reason')->nullable();
            $table->text('solo_parent_needs')->nullable();

            // 🆘 Emergency Contact
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('emergency_address')->nullable();
            $table->string('emergency_contact', 20)->nullable();

            // 📂 Category & Status
            $table->string('category');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');


            // ⏱️ Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // ⚡ Indexes for faster search
            $table->index(['reference_no', 'full_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solo_parent_applications');
    }
};
