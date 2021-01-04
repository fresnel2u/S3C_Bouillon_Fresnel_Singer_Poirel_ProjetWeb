<?php
namespace Whishlist\modele;

use Illuminate\Database\Eloquent\Model;

/**
 * Modele User : represente un utilisateur
 */
class User extends Model{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function listes() {
        return $this->hasMany('wishlist\src\modele\Liste', 'user_id');
    }
}