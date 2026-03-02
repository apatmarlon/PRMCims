<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'note',
    ];

    /**
     * Scope a query to search by name.
     */
    public function scopeSearch($query, $value)
    {
        if (! $value) {
            return $query;
        }

        return $query->where('name', 'like', "%{$value}%");
    }
}
