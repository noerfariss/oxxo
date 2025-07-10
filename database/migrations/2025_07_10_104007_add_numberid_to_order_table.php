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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('numberid')->nullable()->after('uuid');
            $table->foreignId('member_id')->nullable()->after('products')->constrained('members');
            $table->text('membertext')->nullable()->after('member_id');
            $table->decimal('subtotal')->nullable()->after('membertext');
            $table->decimal('discount')->nullable()->after('subtotal');
            $table->decimal('grandtotal')->nullable()->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['numberid', 'membertext', 'subtotal', 'discount', 'grandtotal']);
            $table->dropConstrainedForeignId('member_id');
        });
    }
};
