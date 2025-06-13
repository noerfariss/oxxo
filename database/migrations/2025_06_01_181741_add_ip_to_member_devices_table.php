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
        Schema::table('member_devices', function (Blueprint $table) {
            $table->string('ip')->after('devices')->nullable();
            $table->string('latitude')->nullable()->after('ip');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('address')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_devices', function (Blueprint $table) {
            $table->dropColumn(['ip', 'latitude', 'longitude', 'address']);
        });
    }
};
