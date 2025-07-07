<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->string('g_number', 64);
            $table->dateTime('date');
            $table->date('last_change_date')->nullable();
            $table->string('supplier_article', 64)->nullable();
            $table->string('tech_size', 64)->nullable();
            $table->string('barcode', 64)->nullable();
            $table->decimal('total_price', 15, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->string('warehouse_name', 64);
            $table->string('oblast', 64)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');;
            $table->unsignedBigInteger('income_id');
            $table->unsignedBigInteger('odid');
            $table->BigInteger('nm_id');
            $table->string('subject', 64);
            $table->string('category', 64);
            $table->string('brand', 64);
            $table->boolean('is_cancel');
            $table->dateTime('cancel_dt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
