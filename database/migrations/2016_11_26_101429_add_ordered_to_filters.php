<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderedToFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('filters', function (Blueprint $table) {
            //
            $table->boolean('ordered')->nullable();
            $table->renameColumn('displayName', 'display_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('filters', function (Blueprint $table) {
            //
        });
    }
}
