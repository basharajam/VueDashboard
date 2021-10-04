<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVueLayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // title	
        // value	
        // type	
        // wherePage	
        // compName	
        Schema::create('vue_layouts', function (Blueprint $table) {
            $table->id();
            $table->String('title');
            $table->String('value');
            $table->String('wherePage');
            $table->String('compName');
            $table->String('compType');
            $table->integer('itemNum');
            
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vue_layouts');
    }
}
