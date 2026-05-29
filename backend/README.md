# cgehuzi/core-backend

Composer-пакет для Laravel-проектов на базе [projects-skeleton](../../projects-skeleton).
Архитектурное ядро backend: сервисы, Filament-ресурсы и render-API для headless-фронтенда.

## Установка

Пакет НЕ публикуется в Packagist — ставится из tarball-релиза репозитория
`cgehuzi/package-core`. В `backend/composer.json` проекта:

```json
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "cgehuzi/core-backend",
                "version": "0.0.1",
                "type": "library",
                "dist": {
                    "url": "https://github.com/cgehuzi/package-core/releases/download/v0.0.1/core-backend-v0.0.1.tar.gz",
                    "type": "tar"
                },
                "autoload": {
                    "psr-4": { "Cgehuzi\\Core\\": "src/" }
                },
                "require": {
                    "php": "^8.3",
                    "illuminate/support": "^12.0 || ^13.0",
                    "illuminate/database": "^12.0 || ^13.0",
                    "illuminate/http": "^12.0 || ^13.0",
                    "filament/filament": "^4.0"
                },
                "extra": {
                    "laravel": {
                        "providers": ["Cgehuzi\\Core\\CoreServiceProvider"]
                    }
                }
            }
        }
    ],
    "require": {
        "cgehuzi/core-backend": "0.0.1"
    }
}
```

После `composer install` ServiceProvider подключается автоматически (Laravel package
discovery). Проверка: `GET /api/core/health` → `{ "status": "ok", ... }`.

Опубликовать конфиг (если нужно править дефолты):

```bash
php artisan vendor:publish --tag=core-config
```

## Что внутри (после переноса среза)

- **Контент-модель `Page`** + миграция `pages` (`locale`, `path`, `title`, `status`,
  `redirect_to`, `blocks` jsonb, `seo` jsonb; уникальность `locale+path`). Миграция
  подключается автоматически — на проекте достаточно `php artisan migrate`.
- **Render-эндпоинт** `GET /api/core/render?path=/&locale=ru` → контракт
  `{ status, redirect, locale, route, seo, blocks }` (backend-driven routing).
- **Filament-ресурс `Page`** с Builder-блоками (hero/text). Регистрируется плагином —
  в `app/Providers/Filament/AdminPanelProvider.php` проекта:

  ```php
  use Cgehuzi\Core\Filament\CorePlugin;

  $panel->plugin(CorePlugin::make());
  ```

- Доступ в админку в prod/test требует `App\Models\User implements FilamentUser`
  (в `local` пускает и без него).

## Конфигурация (`config/core.php`)

| Ключ | По умолчанию | Назначение |
|---|---|---|
| `core.default_locale` | `ru` | Локаль по умолчанию (env `CORE_DEFAULT_LOCALE`). |
| `core.register_routes` | `true` | Регистрировать ли дефолтные роуты `/api/core/*`. |

## Локальная разработка

Против проекта удобнее ставить пакет path-репозиторием (без пересборки тарболла):

```json
{ "repositories": [{ "type": "path", "url": "../../package-core/backend" }] }
```
