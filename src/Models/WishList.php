<?php

namespace Whishlist\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    protected $table = 'lists';
    public $timestamps = false;

    protected $casts = [
        'expiration' => 'date'
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'list_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isExpired(): bool
    {
        return $this->expiration->lessThan(new DateTime());
    }
}
