<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sixprofi extends Model
{
    use HasFactory;

    protected $table = 'sixprofis_urls';

    protected $guarded =
        [
            'created_at',
            'updated_at'
        ];
}
