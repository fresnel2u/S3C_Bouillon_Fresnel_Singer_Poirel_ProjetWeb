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
}
