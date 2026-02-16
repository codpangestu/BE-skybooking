<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->softDeletes()->after('airline_id');
        });

        Schema::table('flight_classes', function (Blueprint $table) {
            $table->enum('class_type', ['economy', 'business', 'first'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('flight_classes', function (Blueprint $table) {
            // Revert back to original state if needed, though original had a typo
            $table->enum('class_type', ['economy', 'bussiness'])->change();
        });
    }
};
