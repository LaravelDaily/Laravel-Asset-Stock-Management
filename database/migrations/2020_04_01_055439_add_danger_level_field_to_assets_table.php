<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddDangerLevelFieldToAssetsTable
 */
class AddDangerLevelFieldToAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('assets') && !Schema::hasColumn('assets', 'danger_level')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->integer('danger_level')->default(0);
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
}
