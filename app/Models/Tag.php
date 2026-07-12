<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    public function trades(): BelongsToMany
    {
        return $this->belongsToMany(Trade::class, 'trade_tag');
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'strategy' => 'blue',
            'emotion'  => 'yellow',
            'mistake'  => 'red',
            default    => 'gray',
        };
    }
}
