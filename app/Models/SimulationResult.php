<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationResult extends Model
{
    protected $guarded = [];

    protected $casts = [
      'result' => 'array',
      'completed_at' => 'datetime',
    ];

    public function note() {
        return $this->belongsTo(Note::class);
    }

}
