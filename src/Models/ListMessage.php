<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class ListMessage extends Model
{
    protected $table = 'lists_messages';
    public $timestamps = false;

    public function lists()
    {
        return $this->belongsTo(WishList::class, 'list_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }
}
