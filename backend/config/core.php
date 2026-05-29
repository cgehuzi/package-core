<?php

declare(strict_types=1);

return [
    // Локаль по умолчанию для контента/резолвера маршрутов.
    'default_locale' => env('CORE_DEFAULT_LOCALE', 'ru'),

    // Регистрировать ли дефолтные роуты пакета (/api/core/*). Выключите, если
    // хотите объявить их вручную в проекте.
    'register_routes' => true,
];
