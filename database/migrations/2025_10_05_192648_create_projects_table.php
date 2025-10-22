<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['AHP','Private']);
            $table->enum('status', ['planning','active','on_hold','completed'])->default('active');
            $table->string('consortium');
            $table->string('clientName')->nullable();
            $table->text('description')->nullable();
            $table->date('startDate');
            $table->date('endDate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
