<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    protected $table = 'lists';
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(Item::class, 'list_id', 'id');
    }
}
