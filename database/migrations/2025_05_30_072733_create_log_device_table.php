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
        Schema::create('log_device', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade');
            $table->string('description')->nullable();
            $table->string('ip')->nullable();
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_device');
    }
};
