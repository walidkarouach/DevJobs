<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
        'candidat_id',
        'offre_id',
        'statut'
    ];

    public function candidat()
    {
        return $this->belongsTo(User::class,'candidat_id');
    }

    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }
}