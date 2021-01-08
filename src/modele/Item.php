<?php
namespace Whishlist\modele;

use Illuminate\Database\Eloquent\Model;

/**
 * Modele Item : represente un item d'une liste de souhaits
 */
class Item extends Model{
    protected $table = 'item';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function liste() {
        return $this->belongsTo(liste::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}