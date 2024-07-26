<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [
        'created_at',
        'updated_at'
    ];

    public function getNumberCountAttribute()
    {
        $siteIds = json_decode($this->site_ids, true);
        return Number::whereIn('site_id', $siteIds)
            ->where('active', true)
            ->where('bounced', false)
            ->count();
    }

}
