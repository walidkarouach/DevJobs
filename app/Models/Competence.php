<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competence extends Model
{
        use HasFactory;

    protected $fillable = [
        'nom'
    ];

    public function offres()
    {
        return $this->belongsToMany(Offre::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
