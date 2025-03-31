<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'parameters' => 'array',
        'result' => 'array',
        'analysis' => 'array',
        'example_scenarios' => 'array',
        'completed_at' => 'datetime',
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public static function analyzeResult($result)
    {
        return [
            'mean_irr' => [
                'value' => $result['mean_irr'] ?? null,
                'is_high' => ($result['mean_irr'] ?? 0) > 0.10,
                'description' => "The Average Internal Rate of Return across all the trials. It's the expected annualized return, factoring in variability like defaults, prepayments, or recoveries.
                    A high Average IRR signals strong profit potential. Higher is better.",
            ],
            'median_vs_mean' => [
                'median' => $result['median_irr'] ?? null,
                'mean' => $result['mean_irr'] ?? null,
                'is_median_less' => ($result['median_irr'] ?? 0) < ($result['mean_irr'] ?? 0),
                'description' => "The Median Internal Rate of Return: the middle IRR value (50th percentile) half the trials yield more, half yield less. Removes outliers.
                    Favor notes where the Median IRR is well above you minimum acceptable return (8-10%). Higher is better.",
            ],
            'std_irr' => [  // Changed from std_dev
                'value' => $result['std_irr'] ?? null,
                'is_low' => ($result['std_irr'] ?? 0) < 0.10,
                'description' => "Measures the variability of IRR outcomes. A higher Standard Deviation means returns fluctuate widely indicating higher uncertainty. Lower is better.",
            ],
            'percentile_5' => [
                'value' => $result['percentile_5'] ?? null,
                'is_positive' => ($result['percentile_5'] ?? 0) > 0,
                'description' => "The IRR in the worst 5% of scenarios. If this is positive then it's a green light. Higher is better.",
            ],
            'percentile_95' => [
                'value' => $result['percentile_95'] ?? null,
                'is_high' => ($result['percentile_95'] ?? 0) > 0.20,
                'description' => "The IRR in the best 5% of scenarios. Higher is better.",
            ],
            'prob_loss' => [
                'value' => $result['prob_loss'] ?? null,
                'is_low' => ($result['prob_loss'] ?? 0) < 0.20,
                'description' => "The chance your IRR goes negative, meaning you lose money overall. Set the threshold (< 10% probability of loss) to ensure profit isn't wiped out by bad scenarios. Lower is better.",
            ],
            'prob_above_8' => [  // Changed from prob_irr_above_8
                'value' => $result['prob_above_8'] ?? null,
                'is_high' => ($result['prob_above_8'] ?? 0) > 0.50,
                'description' => "The likelihood your IRR exceeds 8%, a common benchmark for other investments. Higher is better.",
            ],
        ];
    }

    public static function getExampleScenarios($value) {
        // Should add to a config page
        $discounts = [10, 15, 20];
        $purchase_price = [];

        foreach ($discounts as $discount) {
            $discount_percent = $discount/100;
            $purchase_price[$discount] = round($value - ($value * $discount_percent));
        }

        return $purchase_price;
    }
}
