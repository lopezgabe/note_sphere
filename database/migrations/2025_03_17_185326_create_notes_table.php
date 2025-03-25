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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_id');
            $table->bigInteger('listing_price')->nullable();
            $table->bigInteger('upb_initial')->nullable();
            $table->bigInteger('monthly_pi')->nullable();
            $table->integer('term_months')->nullable();
            $table->decimal('interest_rate')->nullable();
            $table->string('url')->nullable();
            $table->string('listing_type')->nullable();
            $table->date('list_date')->nullable();
            $table->string('seller')->nullable();
            $table->string('negotiation_type')->nullable();
            $table->boolean('lien_position')->nullable();
            $table->string('performance')->nullable();
            $table->string('note_type')->nullable();
            $table->decimal('yield')->nullable();
            $table->boolean('interest_only_loan')->nullable();
            $table->string('property_value')->nullable();
            $table->string('property_value_type')->nullable();
            $table->decimal('itb')->nullable();
            $table->decimal('itv')->nullable();
            $table->decimal('ltv')->nullable();
            $table->date('origination_date')->nullable();
            $table->bigInteger('original_balance')->nullable();
            $table->bigInteger('total_payoff')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('property_type')->nullable();
            $table->date('last_payment_received')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->integer('accrued_late_charges')->nullable();
            $table->boolean('hardest_hit_fund')->nullable();
            $table->boolean('judicial_state')->nullable();
            $table->boolean('non_judicial_state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
