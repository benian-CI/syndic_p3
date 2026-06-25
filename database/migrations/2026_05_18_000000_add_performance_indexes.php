<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->index('paid_at');
            $table->index('month');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('spent_at');
        });

        Schema::table('villas', function (Blueprint $table) {
            $table->index('owner_name');
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropIndex(['paid_at']);
            $table->dropIndex(['month']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['spent_at']);
        });

        Schema::table('villas', function (Blueprint $table) {
            $table->dropIndex(['owner_name']);
        });
    }
};
