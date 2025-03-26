<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParametersToSimulationResults extends Migration
{
    public function up()
    {
        Schema::table('simulation_results', function (Blueprint $table) {
            $table->string('name')->nullable()->after('note_id'); // Optional scenario name
            $table->json('parameters')->nullable()->after('name'); // Scenario inputs
        });
    }

    public function down()
    {
        Schema::table('simulation_results', function (Blueprint $table) {
            $table->dropColumn(['name', 'parameters']);
        });
    }
}
