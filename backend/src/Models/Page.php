<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Страница — источник истины по публичному маршруту (backend-driven routing).
 * Контент хранится блочной моделью в формате Filament Builder: [{type, data}].
 */
class Page extends Model
{
    protected $fillable = [
        'locale',
        'path',
        'title',
        'status',
        'redirect_to',
        'blocks',
        'seo',
    ];

    protected $casts = [
        'blocks' => 'array',
        'seo' => 'array',
    ];
}
