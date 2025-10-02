<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'question', 'answer', 'published', 'position'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}



