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
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'month')) {
                $table->date('month')->nullable()->after('title');
            }

            if (! Schema::hasColumn('events', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('month');
            }

            if (! Schema::hasColumn('events', 'paid_at')) {
                $table->date('paid_at')->nullable()->after('amount');
            }

            if (! Schema::hasColumn('events', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('events', 'reference')) {
                $table->string('reference')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            foreach (['reference', 'payment_method', 'paid_at', 'amount', 'month'] as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
