<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExampleScenariosToSimulationResults extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('simulation_results', function (Blueprint $table) {
            $table->json('example_scenarios')->nullable()->after('analysis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulation_results', function (Blueprint $table) {
            $table->dropColumn('example_scenarios');
        });
    }
};
