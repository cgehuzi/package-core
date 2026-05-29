# CLAUDE.md — package-core

Заметки по нюансам реализации. Базовое — в [README.md](README.md).

## Сообщения коммитов

- Ассистент НЕ коммитит/тегает/пушит сам — только предлагает готовый текст commit message
  в конце куска работы; коммитит человек.
- Стиль: **Conventional Commits, на английском**. Допустимые типы:
  `feat`, `fix`, `chore`, `docs`, `refactor`, `test`, `ci`, `build`.
- Заголовок ≤72 символов, повелительное наклонение; тело — пунктами «что/зачем».

## Назначение

Переиспользуемая архитектурная начинка для проектов на base
[projects-skeleton](../projects-skeleton). Подключается **зависимостями** (composer + npm)
из GitHub-релизов, версионируется per-project. Это НЕ оболочка — инфраструктура живёт в
skeleton; здесь модули/ресурсы/контракты.

## Стек

- backend: PHP 8.4, Laravel 13 (поддержка 12||13), Filament 4. Namespace `Cgehuzi\Core`.
- frontend: TypeScript, цель — Next 15/16 + React 19. Имя `@cgehuzi/core-frontend`.

## Организация (один репо, пакеты по директориям)

- `backend/` — composer-пакет, `frontend/` — npm-пакет. Имена директорий = слои skeleton.
- Каждый пакет самодостаточен: свой манифест, README, тесты.

## Дистрибуция — ключевые нюансы

- **composer — tarball релиза, НЕ vcs.** vcs требует `composer.json` в корне репо; у нас
  пакет в поддиректории `backend/`, поэтому раздаём `.tar.gz` ассетом релиза, а проект
  объявляет `repositories: [{type:"package", dist:{url, type:"tar"}}]`.
- **release.yml: backend-тарболл пакуется БЕЗ ведущего `./`.** PharData в composer падает
  на entry с именем `.` ("Cannot extract '.'") → `composer install` ломается. Поэтому в
  workflow перечисляем верхнеуровневые entries (`shopt -s dotglob`), а не `tar -C backend .`.
- **frontend — ship-source.** Пакет отдаёт TS-исходники (`main: src/index.ts`, без сборки);
  потребитель транспилирует через `transpilePackages` в `next.config.ts`. `npm pack` → `.tgz`.
- **Версии:** `Core::VERSION` (PHP) и `version` в `frontend/package.json` синхронизируются с
  тегом вручную (frontend — авто в release.yml через `npm version`).

## Контракт

- Форма ответа API — единственный источник правды в `frontend/src/types.ts`. PHP
  (`cgehuzi/core-backend`) обязан возвращать ровно эти поля. Менять синхронно обе стороны.

## Тесты

- backend: Orchestra Testbench + PHPUnit, sqlite `:memory:` (`tests/TestCase.php` поднимает
  Laravel и регистрирует `CoreServiceProvider`). Запуск в контейнере `Dockerfile.test`.
- frontend: Vitest + `tsc --noEmit`. fetch мокается через `vi.stubGlobal`.
- Локально: `make test` (всё в Docker). На хосте composer/php/node не нужны.

## Локальная разработка против проекта

Проще path-репозиторий, чем пересборка тарболла на каждое изменение:
- composer: `repositories: [{type:"path", url:"../../package-core/backend"}]`
- npm: `npm install ../../package-core/frontend`

## Контент (перенесён из референс-среза)

- backend: `Models\Page` + миграция `pages`; `Http\Controllers\RenderController` →
  `GET /api/core/render`; Filament `Resources\Pages\PageResource` (+ Schemas/Tables/Pages) с
  Builder-блоками hero/text; `Filament\CorePlugin` (регистрация ресурса на панели проекта).
- frontend: типы `Block`/`RenderResult`; `fetchRender`; `resolveRoute`; `BlockRenderer`+`Hero`/`Text`.

### Нюансы переноса

- **render под `/api/core/render`** (не `/api/render`): всё, что регистрирует пакет, живёт
  под префиксом `api/core` (без коллизий с роутами проекта). Фронт-клиент бьёт туда же.
- **`RenderController` не наследует `App\Http\Controllers\Controller`** — в пакете его нет;
  инвокабл-класс работает роут-хендлером напрямую.
- **Filament — hard-зависимость backend-пакета** (`filament/filament`). После правки
  `composer.json` обязательно `composer update` (иначе `composer install` падает на
  устаревшем `composer.lock`: "package filament/filament is not present in the lock file").
- **Filament-ресурс в тестах не бутстрапится** (Testbench без панели) — тесты покрывают
  render-контракт и модель; корректность формы с Builder проверена на стороне skeleton и
  проверяется при интеграции в проект (Phase 3).
- Блоки — формат Filament Builder `[{type, data}]`; фронт-`BlockRenderer` спредит `data`.

## Интеграция в skeleton-проект (проверено end-to-end)

Шаги в проекте:
- backend: composer-репозиторий (path для локалки / tarball для релиза) + `require cgehuzi/core-backend`
  → ServiceProvider автодискаверится (роуты `/api/core/*`, миграции). `php artisan migrate`.
  В `AdminPanelProvider`: `->plugin(\Cgehuzi\Core\Filament\CorePlugin::make())`.
- frontend: установить `@cgehuzi/core-frontend`, добавить `transpilePackages: ["@cgehuzi/core-frontend"]`
  в `next.config.ts`, написать тонкий `app/[[...slug]]/page.tsx` поверх `resolveRoute`+`fetchRender`+`BlockRenderer`.

### Грабли интеграции (важно)

- **npm: ставить ТОЛЬКО tarball'ом, не path/symlink.** Turbopack/Next НЕ резолвит symlink-
  зависимость, указывающую за пределы проекта → "Module not found: @cgehuzi/core-frontend".
  Релизный `.tgz` (или локально `npm pack` → `npm install …tgz`) кладёт реальные файлы — резолвится.
  Поэтому в `frontend/README` локальная разработка тоже через tarball, а не `npm install ../path`.
- **composer path-repo работает** (symlink ок), но пакету нужен `version` в `composer.json`
  (для tarball-дистрибуции игнорируется, нужен только локальному path-repo). Для локалки
  package-core должен быть **примонтирован внутрь контейнеров проекта** (через
  `compose.override.yaml`: `../package-core:/packages/core:ro`), т.к. composer/npm бегут в Docker.
- **`transpilePackages` обязателен** в проекте (пакет — ship-source TS).
- Render живёт под `/api/core/render`; клиент `fetchRender` бьёт туда (env `INTERNAL_API_URL`).

> Замечено при интеграции: в skeleton `make init` шаг `composer require filament/filament`
> иногда падает транзиентно на macOS bind-mount (параллельная распаковка,
> "Failed to open directory …/nette/php-generator") → `filament:install` не отрабатывает.
> Это баг skeleton (не пакета); лечится повтором. Кандидат на фикс: ретрай/--no-parallel.

## Состояние / план

- Каркас + перенос среза + интеграция в реальный проект — готово, всё зелёное.
- Дальше — версия 0.0.1: коммит, тег, первый GitHub Release (тарболлы) для установки в проекты.
