<?php

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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('note_id'); // Links to note
            $table->decimal('listed_price', 15, 2); // Original asking price
            $table->decimal('offered_price', 15, 2)->nullable(); // Your initial offer
            $table->decimal('counter_price', 15, 2)->nullable(); // Their counteroffer
            $table->decimal('final_price', 15, 2)->nullable(); // Agreed price (if accepted)
            $table->decimal('monthly_pi', 15, 2);
            $table->integer('term_months');
            $table->decimal('upb_initial', 15, 2);
            $table->float('interest_rate');
            $table->enum('status', ['offer', 'countered', 'accepted', 'rejected', 'withdrawn'])->default('offer');
            $table->date('offer_date')->nullable();
            $table->date('counter_date')->nullable();
            $table->date('final_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
