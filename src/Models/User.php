<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    public function lists()
    {
        return $this->hasMany(WishList::class, 'list_id', 'id');
    }

    public function reservations()
    {
        return $this->hasMany(ItemReservation::class, 'user_id', 'id');
    }
}
