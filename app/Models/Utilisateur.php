<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    // Nom exact de la table dans la base de données
    protected $table = 'utilisateurs';
    
    // Clé primaire personnalisée
    protected $primaryKey = 'idUtilisateur';
    
    // Les attributs qui peuvent être assignés en masse (Mass Assignment)
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'provider_name',
        'provider_id',
        'est_en_ligne',
        'role',
        'date_inscription'
    ];
    
    // Masquer le mot de passe dans les réponses JSON
    protected $hidden = [
        'mot_de_passe',
    ];
}
