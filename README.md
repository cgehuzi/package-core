# package-core

Базовые архитектурные пакеты для проектов на основе
[projects-skeleton](../projects-skeleton). Один репозиторий — два независимых пакета,
названных консистентно слоям skeleton:

| Подпакет | Тип | Имя | Назначение |
|---|---|---|---|
| [backend/](backend/) | composer | `cgehuzi/core-backend` | Laravel-сервисы, Filament-ресурсы, render-API для headless-фронтенда. |
| [frontend/](frontend/) | npm | `@cgehuzi/core-frontend` | TypeScript-типы и клиент для вызова render-API из Next.js. |

## Дистрибуция (без Packagist/npm)

Релиз (`vX.Y.Z`) создаётся вручную на GitHub; по событию его публикации
[release-workflow](.github/workflows/release.yml) прикладывает к нему два ассета:

- `core-backend-vX.Y.Z.tar.gz` — composer ставит через `repositories: [{type:"package", dist:…}]`
  (см. [backend/README.md](backend/README.md#установка));
- `core-frontend-vX.Y.Z.tgz` — npm ставит по URL ассета
  (см. [frontend/README.md](frontend/README.md#установка)).

Проект пинит конкретную версию и обновляет её осознанно (обычная зависимость).

## Релиз

1. Внесли изменения, прогнали `make test`.
2. Обновили `Core::VERSION` (backend) и `version` в `frontend/package.json`, дописали `CHANGELOG.md`.
3. Создали Release на GitHub (тег `vX.Y.Z` + заметки) вручную → workflow по событию
   `release: published` соберёт и приложит к нему архивы.

Семвер: мажор — breaking changes контракта (`/api/render`, типы), минор — фича, патч — багфикс.

## Разработка и тесты

Тесты — в Docker, на хосте ничего не нужно:

```bash
make test            # backend (PHPUnit+Testbench) + frontend (tsc+Vitest)
make test-backend
make test-frontend
```

Контракт ответа API обязан совпадать между PHP (`cgehuzi/core-backend`) и TS
(`frontend/src/types.ts`) — расхождение ломает потребителей. Подробности и подводные
камни — в [CLAUDE.md](CLAUDE.md).

## Структура

```
.
├── backend/                 composer-пакет (Cgehuzi\Core)
│   ├── composer.json · src/ · config/ · routes/ · database/migrations/
│   ├── tests/ · phpunit.xml · Dockerfile.test · README.md
├── frontend/                npm-пакет (@cgehuzi/core-frontend)
│   ├── package.json · tsconfig.json · src/ · tests/ · README.md
├── .github/workflows/       ci.yml (тесты), release.yml (публикация архивов)
├── docker-compose.test.yml  тест-окружение
├── Makefile · README.md · CLAUDE.md · CHANGELOG.md
```
