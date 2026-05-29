<?php

declare(strict_types=1);

use Cgehuzi\Core\Core;
use Cgehuzi\Core\Http\Controllers\RenderController;
use Illuminate\Support\Facades\Route;

// Префикс api/core и middleware 'api' навешивает CoreServiceProvider.

// Backend-driven routing: SSR-слой (Next.js) спрашивает, что показать по URL.
//   GET /api/core/render?path=/&locale=ru
Route::get('render', RenderController::class);

// Диагностика: пакет подключился и отвечает.
Route::get('health', fn () => response()->json([
    'package' => 'cgehuzi/core-backend',
    'version' => Core::VERSION,
    'status' => 'ok',
]));
