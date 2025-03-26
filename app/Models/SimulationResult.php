<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationResult extends Model
{
    protected $fillable = ['note_id', 'name', 'parameters', 'result', 'analysis', 'completed_at'];

    protected $casts = [
        'parameters' => 'array',
        'result' => 'array',
        'analysis' => 'array',
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
            ],
            'median_vs_mean' => [
                'median' => $result['median_irr'] ?? null,
                'mean' => $result['mean_irr'] ?? null,
                'is_median_less' => ($result['median_irr'] ?? 0) < ($result['mean_irr'] ?? 0),
            ],
            'std_irr' => [  // Changed from std_dev
                'value' => $result['std_irr'] ?? null,
                'is_low' => ($result['std_irr'] ?? 0) < 0.10,
            ],
            'percentile_5' => [
                'value' => $result['percentile_5'] ?? null,
                'is_positive' => ($result['percentile_5'] ?? 0) > 0,
            ],
            'percentile_95' => [
                'value' => $result['percentile_95'] ?? null,
                'is_high' => ($result['percentile_95'] ?? 0) > 0.20,
            ],
            'prob_loss' => [
                'value' => $result['prob_loss'] ?? null,
                'is_low' => ($result['prob_loss'] ?? 0) < 0.20,
            ],
            'prob_above_8' => [  // Changed from prob_irr_above_8
                'value' => $result['prob_above_8'] ?? null,
                'is_high' => ($result['prob_above_8'] ?? 0) > 0.50,
            ],
        ];
    }
}
