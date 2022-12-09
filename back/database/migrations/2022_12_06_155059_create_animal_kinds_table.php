<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAnimalKindsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animal_kinds', function (Blueprint $table) {
            $table->id();
            $table->string('kind');
            $table->integer('max_size');
            $table->integer('max_age');
            $table->decimal('growth_factor', 8, 2);
            $table->timestamps();
        });

        DB::table('animal_kinds')->insert([
            [
                'kind' => 'cat',
                'max_size' => 25,
                'max_age' => 10,
                'growth_factor' => 1.3
            ],[
                'kind' => 'dog',
                'max_size' => 40,
                'max_age' => 15,
                'growth_factor' => 1.7
            ],[
                'kind' => 'bird',
                'max_size' => 10,
                'max_age' => 5,
                'growth_factor' => 1.1
            ],[
                'kind' => 'bear',
                'max_size' => 50,
                'max_age' => 20,
                'growth_factor' => 2.2
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animal_kinds');
    }
}
