<?php

namespace Whishlist\modele;

use Illuminate\Database\Eloquent\Model;

/**
 * Modele Liste : represente une liste de souhaits
 */
class Liste extends Model
{
    protected $table = 'liste';
    protected $primaryKey = 'no';
    public $timestamps = false;

    public function items() {
        return $this->hasMany(Item::class);
    }

    public function foundingPot()
    {
        return $this->hasOne(FoundingPot::class, 'liste_id');
    }
}
