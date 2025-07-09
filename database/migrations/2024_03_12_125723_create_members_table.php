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
            $table->string('numberid')->index()->nullable();
            $table->string('name')->index();
            $table->string('phone')->unique()->nullable();
            $table->date('born')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->unsignedTinyInteger('gender')->default(0);
            $table->string('password');
            $table->boolean('is_member')->default(false);
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
