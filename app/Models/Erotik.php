<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Erotik extends Model
{
    use HasFactory;

    protected $guarded =
        [
          'created_at',
          'update_at'
        ];
}
