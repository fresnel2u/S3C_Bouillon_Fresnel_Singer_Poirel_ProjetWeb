<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class FoundingPot extends Model
{
    protected $table = 'founding_pots';
    public $timestamps = false;

    public function list()
    {
        return $this->belongsTo(WishList::class, 'list_id', 'id');
    }
}
