<?php

namespace Whishlist\modele;

use Illuminate\Database\Eloquent\Model;

/**
 * Modele Item : represente un item d'une liste de souhaits
 */
class FoundingPot extends Model
{
    protected $table = 'cagnotte';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function liste()
    {
        return $this->belongsTo(Liste::class, 'liste_id');
    }
}
