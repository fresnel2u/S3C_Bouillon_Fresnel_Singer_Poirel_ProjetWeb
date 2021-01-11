<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    public $timestamps = false;

    public function list()
    {
        return $this->belongsTo(WishList::class, 'list_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function foundingPot()
    {
        return $this->hasOne(FoundingPot::class, 'item_id', 'id');
    }

    public function reservation()
    {
        return $this->hasOne(ItemReservation::class, 'item_id', 'id');
    }
}
