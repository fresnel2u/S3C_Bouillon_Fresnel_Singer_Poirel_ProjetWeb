<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class ItemReservation extends Model
{
    protected $table = 'items_reservations';
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
