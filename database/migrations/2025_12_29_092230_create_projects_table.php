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
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('project_manager_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->date('deadline')->nullable();
            $table->enum('status', ['active', 'completed', 'on_hold'])
                  ->default('active');
            $table->decimal('progress', 5, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};