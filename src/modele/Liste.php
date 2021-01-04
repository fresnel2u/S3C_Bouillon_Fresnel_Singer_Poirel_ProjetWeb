<?php
namespace Whishlist\modele;

use Illuminate\Database\Eloquent\Model;

/**
 * Modele Liste : represente une liste de souhaits
 */
class Liste extends Model{
    protected $table = 'liste';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function items() {
        return $this->hasMany('wishlist\src\modele\Item', 'liste_id');
    }
}