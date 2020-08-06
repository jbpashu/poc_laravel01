<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            // V3 Order Id
            $table->bigInteger('order_id')->unsigned()->index();
            $table->bigInteger('item_id');
            $table->unsignedInteger('line');
            //Product id
            $table->unsignedInteger('external_id');
            $table->unsignedInteger('ordered_quantity');
            $table->unsignedInteger('allocated_quantity')->nullable();
            $table->unsignedInteger('picked_quantity')->nullable();
            $table->unsignedInteger('validated_quantity')->nullable();
            $table->unsignedInteger('packed_quantity')->nullable();
            $table->string('UOM')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            // V3 Item id
            // $table->foreign('item_id')->references('id')->on('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_lines');
    }
}
