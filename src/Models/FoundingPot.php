<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class FoundingPot extends Model
{
    protected $table = 'founding_pots';
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function participations()
    {
        return $this->hasMany(FoundingPotParticipation::class, 'founding_pot_id', 'id');
    }

    public function getParticipationsTotal(): float
    {
        $sum = 0.0;
        foreach ($this->participations as $participation) {
            $sum += $participation->amount;
        }
        return round($sum, 2);
    }

    public function getRest(): float
    {
        return round($this->amount - $this->getParticipationsTotal(), 2);
    }
}
