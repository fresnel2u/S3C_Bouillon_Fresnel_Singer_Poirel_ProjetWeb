<?php

namespace Whishlist\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    public function lists()
    {
        return $this->hasMany(WishList::class);
    }

    public function reservations()
    {
        return $this->hasMany(ItemReservation::class, 'user_id', 'id');
    }

    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function editableLists()
    {
        return $this->belongsToMany(WishList::class, UsersLists::class, 'user_id', 'list_id', 'id', 'id');
    }

    /**
     * Listes modifiables mais qui ne nous appartiennent pas
     *
     * @return Collection
     */
    public function invitedLists()
    {
        return $this->belongsToMany(WishList::class, UsersLists::class, 'user_id', 'list_id', 'id', 'id')
                ->where('lists.user_id', '!=', $this->id);
    }
}
