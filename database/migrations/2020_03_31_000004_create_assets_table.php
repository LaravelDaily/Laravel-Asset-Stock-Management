<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->double('price_buy', 8, 2)->default(0);
            $table->double('price_sell', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
