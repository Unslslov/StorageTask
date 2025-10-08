<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->string('sale_id',64);
            $table->string('g_number', 64);
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article', 64);
            $table->string('tech_size', 64);
            $table->string('barcode', 64);
            $table->decimal('total_price', 15, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->decimal('promo_code_discount', 15, 2)->nullable();
            $table->string('warehouse_name', 64);
            $table->string('country_name', 64);
            $table->string('oblast_okrug_name', 64);
            $table->string('region_name', 64);
            $table->foreignId('income_id');
            $table->unsignedBigInteger('odid')->nullable();
            $table->decimal('spp', 5, 2);
            $table->decimal('for_pay', 15, 2);
            $table->decimal('finished_price', 15, 2);
            $table->decimal('price_with_disc', 15, 2);
            $table->BigInteger('nm_id')->nullable();
            $table->string('subject', 64);
            $table->string('category', 64);
            $table->string('brand', 64);
            $table->boolean('is_storno')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
