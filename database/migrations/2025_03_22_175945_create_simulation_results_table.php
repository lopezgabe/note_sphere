<?php

use App\Http\Controllers\NoteController;
use App\Models\Note;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulation_results', function (Blueprint $table) {
            $table->id();
            $table->string('note_id');
            $table->json('result');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

// Foreign key constraint
//            $table->foreign('note_id')->references('note_id')->on('notes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_results');
    }
};
