/**
 * Контракт JSON-ответов Laravel-ядра. Единственный источник правды формы ответа —
 * PHP-сторона (cgehuzi/core-backend) обязана возвращать ровно эти поля. Расхождение
 * ловится контракт-тестом проекта.
 */

export type HealthResponse = {
  package: string;
  version: string;
  status: "ok";
};

export type ApiResponse<T> =
  | { ok: true; status: number; data: T }
  | { ok: false; status: number; error: unknown };

/** Блок контента — формат Filament Builder: { type, data }. */
export type Block = {
  type: string;
  data: Record<string, unknown>;
};

/** Ответ render-эндпоинта `/api/core/render`. */
export type RenderResult = {
  /** 200 | 301 | 302 | 404 — логический статус, который SSR эмитит браузеру. */
  status: number;
  redirect: string | null;
  locale: string;
  route: { type: string; id: number } | null;
  seo: { title?: string; description?: string; canonical?: string } | null;
  blocks: Block[];
};
