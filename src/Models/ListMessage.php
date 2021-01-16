<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class ListMessage extends Model
{
    protected $table = 'lists_messages';
    public $timestamps = false;

    public function list()
    {
        return $this->belongsTo(WishList::class, 'list_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
