/**
 * Тонкий клиент над render-API ядра. Не зависит от next/* сознательно: модуль
 * импортируется из любого окружения (Server Component, Route Handler, браузер).
 * Базовый URL берётся из опции или env (в skeleton — INTERNAL_API_URL = http://api-internal/api).
 */

import type { ApiResponse, HealthResponse, RenderResult } from "./types";

export type FetchOptions = {
  /** Базовый URL API ядра. Если не задан — берётся из env INTERNAL_API_URL. */
  baseUrl?: string;
  /** Поведение кеша fetch. По умолчанию no-store (контент меняется в любой момент). */
  cache?: RequestCache;
  /** Расширение Next.js (revalidate, tags) — передаётся как есть. */
  next?: { revalidate?: number | false; tags?: string[] };
};

function resolveBaseUrl(opts: FetchOptions): string {
  if (opts.baseUrl) return opts.baseUrl;
  const fromEnv =
    (typeof process !== "undefined" && process.env?.INTERNAL_API_URL) || undefined;
  if (fromEnv) return fromEnv;
  throw new Error(
    "[@cgehuzi/core-frontend] baseUrl не задан: передайте опцию или установите env INTERNAL_API_URL.",
  );
}

/** Диагностика: пакет core-backend подключён и отвечает. */
export async function fetchHealth(
  opts: FetchOptions = {},
): Promise<ApiResponse<HealthResponse>> {
  const base = resolveBaseUrl(opts).replace(/\/+$/, "");
  const res = await fetch(`${base}/core/health`, {
    method: "GET",
    headers: { Accept: "application/json" },
    cache: opts.cache ?? "no-store",
    ...(opts.next ? { next: opts.next } : {}),
  });
  const body = await res.json().catch(() => ({}));
  return res.ok
    ? { ok: true, status: res.status, data: body as HealthResponse }
    : { ok: false, status: res.status, error: body };
}

/**
 * Резолвинг страницы по URL (backend-driven routing). Возвращает контракт целиком;
 * логический статус — в поле `status`. Бросает только при транспортной/серверной ошибке.
 *
 * Кеш по умолчанию: dev — no-store (свежие данные при разработке контента),
 * prod — ISR с тегом `page:<locale>:<path>` (инвалидация revalidateTag из Filament).
 */
export async function fetchRender(
  path: string,
  locale: string,
  opts: FetchOptions = {},
): Promise<RenderResult> {
  const base = resolveBaseUrl(opts).replace(/\/+$/, "");
  const url = `${base}/core/render?path=${encodeURIComponent(path)}&locale=${encodeURIComponent(locale)}`;

  const init: RequestInit & { next?: FetchOptions["next"] } =
    opts.cache || opts.next
      ? { cache: opts.cache, ...(opts.next ? { next: opts.next } : {}) }
      : process.env.NODE_ENV === "production"
        ? { next: { tags: [`page:${locale}:${path}`], revalidate: 3600 } }
        : { cache: "no-store" };

  const res = await fetch(url, init);
  if (!res.ok) {
    throw new Error(`[@cgehuzi/core-frontend] render failed: HTTP ${res.status}`);
  }
  return res.json() as Promise<RenderResult>;
}
