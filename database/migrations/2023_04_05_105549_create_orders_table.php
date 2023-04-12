<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('address_id')->nullable()->unsigned(); // if choose Drive thru / personalCar the addressid is null 
            $table->bigInteger('used_coupon')->nullable()->unsigned(); // null -> not used / 1/2/3...  used by relations
           
            $table->tinyInteger('order_type')->default(0); // 0=>delivery / 1=>carthru - personalcar
            $table->integer('payment_method')->default(0); // 0=> cach / 1-card 
            
            $table->integer('order_price_delivery')->default(0);
            $table->integer('order_price');

            $table->integer('total_price')->default(0);

            
            $table->integer('coupon_discount');
            $table->tinyInteger('status')->default(0);  // 


            
            $table->dateTime('order_date');


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('address')->onDelete('cascade');
            $table->foreign('used_coupon')->references('id')->on('coupon')->onDelete('cascade');


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
};
