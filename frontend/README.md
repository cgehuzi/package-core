# @cgehuzi/core-frontend

npm-пакет для Next.js-проектов на базе [projects-skeleton](../../projects-skeleton).
TypeScript-типы и тонкий клиент для render-API ядра (`cgehuzi/core-backend`).

Пакет отдаёт **TypeScript-исходники** (без сборки) — потребитель транспилирует их сам.

## Установка

Из tarball-релиза репозитория `cgehuzi/package-core`. В `frontend/package.json` проекта:

```json
{
  "dependencies": {
    "@cgehuzi/core-frontend": "https://github.com/cgehuzi/package-core/releases/download/v0.0.1/core-frontend-v0.0.1.tgz"
  }
}
```

Так как пакет — TS-исходники, включите его в транспиляцию Next (`frontend/next.config.ts`):

```ts
const nextConfig = {
  transpilePackages: ["@cgehuzi/core-frontend"],
};
```

Локальная разработка против проекта — через локальный tarball (НЕ path/symlink:
Turbopack не резолвит symlink за пределами проекта → "Module not found"):

```bash
# собрать .tgz из пакета и поставить в проект
(cd ../../package-core/frontend && npm pack --pack-destination /tmp)
npm install /tmp/cgehuzi-core-frontend-0.0.1.tgz
```

И обязательно `transpilePackages: ["@cgehuzi/core-frontend"]` в `next.config.ts` проекта.

## Использование

```ts
import { fetchHealth } from "@cgehuzi/core-frontend";

// baseUrl из env INTERNAL_API_URL (в skeleton — http://api-internal/api) или явной опцией
const res = await fetchHealth();
if (res.ok) console.log(res.data.status); // "ok"
```

## Контракт

Формы ответов API описаны в [`src/types.ts`](src/types.ts) — единственный источник
правды. PHP-сторона (`cgehuzi/core-backend`) обязана возвращать ровно эти поля.
