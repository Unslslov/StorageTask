<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToDataTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_tables', function (Blueprint $table) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('account_id')->after('sale_id')->nullable()->constrained()->onDelete('cascade');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('account_id')->after('g_number')->nullable()->constrained()->onDelete('cascade');
            });

            Schema::table('stocks', function (Blueprint $table) {
                $table->foreignId('account_id')->after('barcode')->nullable()->constrained()->onDelete('cascade');
            });

            Schema::table('incomes', function (Blueprint $table) {
                $table->foreignId('account_id')->after('income_id')->nullable()->constrained()->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
}
