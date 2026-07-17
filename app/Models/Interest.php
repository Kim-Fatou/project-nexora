<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interest extends Model
{
    // Permet de remplir facilement ces colonnes depuis notre code
    protected $fillable = ['slug', 'name', 'category'];

    // Relation : Un intérêt appartient à plusieurs utilisateurs
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
