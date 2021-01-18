<?php

namespace Whishlist\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class UsersLists extends Model
{
    protected $table = 'users_lists';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function list() {
        return $this->belongsTo(WishList::class, 'list_id');
    }
}
