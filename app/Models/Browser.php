<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Browser extends Model
{
    /**
     * @return HasMany<Click, $this>
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }
}
