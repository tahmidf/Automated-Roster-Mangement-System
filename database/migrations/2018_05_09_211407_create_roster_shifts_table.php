<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRosterShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('roster_id');
            $table->string('employee_id');
            $table->string('date');
            $table->string('shift_time');
            $table->string('shift');
            $table->string('review')->default(-1);
            $table->string('created_by');
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
        Schema::dropIfExists('roster_shifts');
    }
}
