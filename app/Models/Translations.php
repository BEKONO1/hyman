<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translations extends Model
{

    protected $fillable = ['locale', 'attribute', 'value'];

    public function translatable()
    {
        return $this->morphTo();
    }
}

