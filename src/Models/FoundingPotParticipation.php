<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class FoundingPotParticipation extends Model
{
    protected $table = 'founding_pots_participations';
    public $timestamps = false;

    public function foundingPot()
    {
        return $this->belongsTo(FoundingPot::class, 'founding_pot_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
