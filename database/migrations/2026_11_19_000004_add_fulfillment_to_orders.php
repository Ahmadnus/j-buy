<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Orders can now be delivered to the customer OR picked up from a branch.
 * The customer's GPS location is stored so the admin can see it on a map.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('fulfillment_type', 20)->default('delivery')->after('payment_method');
            $table->foreignId('branch_id')->nullable()->after('fulfillment_type')
                  ->constrained('branches')->nullOnDelete();
            // Snapshot so the order still shows the branch if it is renamed/deleted
            $table->string('branch_name', 255)->nullable()->after('branch_id');
            $table->decimal('latitude', 10, 7)->nullable()->after('customer_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            $table->index('fulfillment_type');
        });

        // Pickup orders have no delivery city/address
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_city', 255)->nullable()->change();
            $table->text('customer_address')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['fulfillment_type']);
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn(['fulfillment_type', 'branch_name', 'latitude', 'longitude']);
        });
    }
};
