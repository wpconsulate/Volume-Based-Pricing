<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('merchant_config', function (Blueprint $table) {
             $table->increments('id');
             $table->string('shop_name')->unique();
             $table->string('access_token')->nullable();
             $table->string('nonce')->nullable()->unique();
             $table->string('active')->default(1);
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
         Schema::drop('merchant_config');
     }
}
