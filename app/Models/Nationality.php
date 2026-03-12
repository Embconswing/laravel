<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $table = 'nationality';   // ✅ fixed quote
    protected $primaryKey = 'Nationid'; // ✅ correct PK
    public $timestamps = false;         // ✅ table has no timestamps

    protected $fillable = [
        'Nationname',
        'Nvisacode',
        'Nationcode',
    ];
}
