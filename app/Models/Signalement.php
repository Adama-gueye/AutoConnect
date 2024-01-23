<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = ['description','user_id','annonce_id'];//description

    public function user() 
    {
        return $this -> belongsTo(User::class);
    }
}
