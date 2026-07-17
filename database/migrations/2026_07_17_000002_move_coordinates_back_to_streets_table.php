<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('description');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        Schema::table('villas', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('villas', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('notes');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }
};
