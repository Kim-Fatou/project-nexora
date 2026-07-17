<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    protected $fillable = ['code', 'name'];

    // Relation : une langue est parlée par plusieurs utilisateurs.
    // withPivot('is_native') : on récupère aussi le flag "langue native"
    // directement depuis la ligne pivot, sans requête supplémentaire.
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_native');
    }
}