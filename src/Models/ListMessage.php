<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Model;

class ListMessage extends Model
{
    protected $table = 'lists_messages';
    protected $primaryKey = ['list_id', 'user_id'];
    public $timestamps = false;

    public function lists()
    {
        return $this->belongsToMany(WishList::class, 'id', 'list_id');
    }

    public function users()
    {
        return $this->belongsToMany(Users::class, 'id', 'user_id');
    }
}
