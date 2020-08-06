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
            $table->id();
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('backorder_id')->nullable();
            $table->dateTime('order_date');
            $table->string('order_reference_1')->nullable();
            $table->dateTime('requested_date')->nullable();
            $table->string('status_text')->nullable();
            $table->unsignedInteger('status_code');
            $table->unsignedInteger('source_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('pick_ticket_printed')->nullable();
            $table->timestamp('pick_ticket_printed_on')->nullable();
            $table->timestamp('packaged_on')->nullable();
            $table->boolean('hold_flag')->default(false);
            $table->timestamp('delivery_date')->nullable();
            $table->integer('total_amount')->default('0');
            $table->integer('total_discount')->default('0');
            $table->integer('total_tax')->default('0');
            $table->integer('total_shipping')->default('0');
            $table->unsignedInteger('status_id')->nullable();
            $table->bigInteger('client_id')->unsigned()->index();
            $table->bigInteger('warehouse_id')->unsigned()->index();

            //Client code will be fetched using cliet code
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            //Ship From
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
          

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
        Schema::dropIfExists('orders');
    }
}
