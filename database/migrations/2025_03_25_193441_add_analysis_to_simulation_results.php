<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnalysisToSimulationResults extends Migration
{
    public function up()
    {
        Schema::table('simulation_results', function (Blueprint $table) {
            $table->json('analysis')->nullable()->after('result');
        });
    }

    public function down()
    {
        Schema::table('simulation_results', function (Blueprint $table) {
            $table->dropColumn('analysis');
        });
    }
}
