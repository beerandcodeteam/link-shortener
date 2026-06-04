<?php

namespace App\Models;

use Database\Factories\BrowserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug'])]
class Browser extends Model
{
    /** @use HasFactory<BrowserFactory> */
    use HasFactory;
}
