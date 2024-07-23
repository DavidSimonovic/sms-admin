<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $guarded =
        [
            'created_at',
            'updated_at'
        ];

    public function script()
    {
        return $this->hasOne(Script::class);
    }
}
