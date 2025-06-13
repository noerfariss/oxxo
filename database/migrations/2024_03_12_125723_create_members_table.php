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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->uuid()->index();
            $table->string('nik')->index()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('name')->index();
            $table->unsignedTinyInteger('gender')->default(0);
            $table->string('password');
            $table->integer('salary')->default(0);
            $table->string('remember_token')->nullable();
            $table->string('verified_number')->nullable();
            $table->timestamp('time_verified')->nullable();
            $table->timestamp('expired_verified')->nullable();
            $table->unsignedTinyInteger('verified_count')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
