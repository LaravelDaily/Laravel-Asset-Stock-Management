<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('asset_id');
            $table->foreign('asset_id', 'asset_fk_1230972')->references('id')->on('assetts');
            $table->unsignedInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_1230977')->references('id')->on('teams');
        });

    }
}
