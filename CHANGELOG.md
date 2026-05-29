# Changelog

История изменений package-core. Проекты ориентируются на неё при обновлении версии
зависимостей. Формат — по релизным тегам (`vX.Y.Z`).

## [Unreleased]

### Changed

- `release.yml`: триггер `push: tags` → `release: published`. Релиз создаётся вручную
  на GitHub, workflow лишь собирает и прикладывает к нему тарболлы (checkout по тегу релиза).

## [0.0.1] — 2026-05-29

Первый релиз ядра: каркас пакетов, контент-срез CMS→API→SSR, проверенная интеграция
в skeleton-проект.

### Added

- Каркас монорепо: `backend/` (composer `cgehuzi/core-backend`, ns `Cgehuzi\Core`) и
  `frontend/` (npm `@cgehuzi/core-frontend`).
- backend: `CoreServiceProvider` (конфиг, миграции, роуты), `config/core.php`,
  диагностический `GET /api/core/health`.
- frontend: типы (`HealthResponse`, `ApiResponse`), клиент `fetchHealth` (фреймворк-независим,
  ship-source TS).
- Тест-обвязка: Orchestra Testbench + PHPUnit (backend), Vitest + tsc (frontend),
  `docker-compose.test.yml`, `Makefile`.
- CI (`ci.yml`) и публикация архивов в GitHub Release (`release.yml`).
- backend: модель `Page` + миграция `pages`, render-эндпоинт `GET /api/core/render`
  (контракт `{status, redirect, locale, route, seo, blocks}`), Filament-ресурс `Page`
  с Builder-блоками (hero/text) + `CorePlugin` для регистрации на панели проекта.
  Зависимость `filament/filament: ^4.0`.
- frontend: типы `Block`/`RenderResult`, клиент `fetchRender`, резолвер `resolveRoute`,
  реестр блоков `BlockRenderer` + компоненты `Hero`/`Text`.
- Интеграция в skeleton-проект проверена end-to-end (CMS→API→SSR, Filament-ресурс,
  catch-all). npm-пакет ставится tarball'ом, composer — path/tarball.
