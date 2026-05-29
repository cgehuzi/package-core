/**
 * Разбор catch-all сегмента Next в (locale, path) для render-эндпоинта.
 * Если первый сегмент — известная локаль, он трактуется как префикс локали,
 * иначе берётся локаль по умолчанию.
 */

export const LOCALES = ["ru", "en"] as const;
export const DEFAULT_LOCALE = "ru";

export function resolveRoute(slug?: string[]): { locale: string; path: string } {
  const segments = slug ?? [];
  let locale: string = DEFAULT_LOCALE;
  let rest = segments;

  if (segments.length > 0 && (LOCALES as readonly string[]).includes(segments[0])) {
    locale = segments[0];
    rest = segments.slice(1);
  }

  const joined = "/" + rest.join("/");
  const path = joined === "/" ? "/" : joined.replace(/\/$/, "");

  return { locale, path };
}
