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
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('first_name')->nullable()->after('surname');
            $table->string('contact_number')->nullable()->after('email');
            $table->string('region')->nullable()->after('contact_number');
            $table->string('city')->nullable()->after('region');
            $table->string('barangay')->nullable()->after('city');
            $table->string('street')->nullable()->after('barangay');
            $table->string('landmark')->nullable()->after('street');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'surname',
                'first_name',
                'contact_number',
                'region',
                'city',
                'barangay',
                'street',
                'landmark',
            ]);
        });
    }
};
