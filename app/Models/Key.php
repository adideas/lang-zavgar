<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Key extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_id', 'name', 'description', 'indexed', 'parent'
    ];

    protected $casts = [
        'indexed' => 'array'
    ];
}
