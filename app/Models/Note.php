<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{

    protected $guarded = [];

    protected $casts = [
        'list_date' => 'date',
        'property_value_date' => 'date',
        'origination_date' => 'date',
        'last_payment_received' => 'date',
        'next_payment' => 'date',
        'maturity_date' => 'date',
        'interest_only_loan' => 'boolean',
        'hardest_hit_fund' => 'boolean',
    ];

    public function simulationResults() {
        return $this->hasMany(SimulationResult::class);
    }


    // Accessor for listing_price (cents to dollars)
    public function getListingPriceAttribute($value)
    {
        return $this->getNumberCurrency($value);
    }

    // Accessor for upb_initial (cents to dollars)
    public function getUpbInitialAttribute($value)
    {
        return $this->getNumberCurrency($value);
    }

    // Accessor for monthly_pi (cents to dollars)
    public function getMonthlyPiAttribute($value)
    {
        return $this->getNumberCurrency($value);
    }

    // Accessor for term_months (plain integer, optional formatting)
    public function getTermMonthsAttribute($value)
    {
        return $this->getNumber($value);
    }

    // Accessor for interest_rate (decimal to percentage)
    public function getInterestRateAttribute($value)
    {
        return $value !== null ? number_format($value * 100, 2, '.', '') . '%' : 'N/A';
    }

    // Accessor for ITB (percentage)
    public function getItbAttribute($value)
    {
        return $this->getNumberPercent($value);
    }

    // Accessor for ITV (percentage)
    public function getItvAttribute($value)
    {
        return $this->getNumberPercent($value);
    }

    // Accessor for LTV (percentage)
    public function getLtvAttribute($value)
    {
        return $this->getNumberPercent($value);
    }

    // Accessor for Yield (percentage)
    public function getYieldAttribute($value)
    {
        return $this->getNumberPercent($value);
    }

    // Accessor for Original Balance (percentage)
    public function getOriginalBalanceAttribute($value)
    {
        return $this->getNumberCurrency($value);
    }

    // Accessor for Total Payoff (percentage)
    public function getTotalPayoffAttribute($value)
    {
        return $this->getNumberCurrency($value);
    }


    public function getNumberCurrency($value): string {
        return $value !== NULL ? '$' . number_format($value / 100, 2, '.', ',') : 'N/A';
    }

    public function getNumberPercent($value): string {
        return $value !== NULL ? number_format($value * 100, 2, '.', '') . '%' : 'N/A';
    }

    public function getNumber($value): string {
        return number_format($value, 0, '.', ',') ?? 'N/A'; // Or number_format($value, 0, '.', ',') for "1,234"
    }


}

