<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // person requesting
            $table->date('request_date')->default(now());
            $table->string('request_type'); // e.g., "Operational", "Project"
            $table->string('expense_type'); // e.g., "Supplies", "Travel"
            $table->text('purpose'); // description / purpose
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('KES');
            $table->unsignedBigInteger('project_id')->nullable(); // linked project or ops code
            $table->string('payee_name')->nullable();
            $table->string('payment_mode')->nullable(); // cash, bank, mobile
            $table->string('account_details')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('receipt')->nullable(); // path to uploaded file
            $table->string('status')->default('pending'); // pending / approved / rejected
            $table->unsignedBigInteger('approved_by')->nullable(); // admin who approved
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cashes');
    }
};
