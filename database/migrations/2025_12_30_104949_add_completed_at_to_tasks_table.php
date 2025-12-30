<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Tambahkan kolom completed_at
            $table->timestamp('completed_at')->nullable()->after('progress');
            
            // Optional: tambahkan index untuk performa query
            $table->index(['status', 'completed_at']);
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};