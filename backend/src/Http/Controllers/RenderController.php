<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Http\Controllers;

use Cgehuzi\Core\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Render-резолвер: по URL и локали отдаёт за один запрос всё, что нужно SSR-слою
 * (Next.js) для отрисовки страницы. Это и есть backend-driven routing.
 *
 * Эндпоинт всегда отвечает HTTP 200 (это data-API), а логический статус, который
 * SSR должен эмитить в браузер, лежит в поле `status` контракта (200|301|302|404).
 */
class RenderController
{
    public function __invoke(Request $request): JsonResponse
    {
        $path = $this->normalizePath((string) $request->query('path', '/'));
        $locale = (string) $request->query('locale', (string) config('core.default_locale', 'ru'));

        $page = Page::query()
            ->where('locale', $locale)
            ->where('path', $path)
            ->where('status', 'published')
            ->first();

        if (! $page) {
            return response()->json([
                'status' => 404,
                'redirect' => null,
                'locale' => $locale,
                'route' => null,
                'seo' => null,
                'blocks' => [],
            ]);
        }

        if ($page->redirect_to) {
            return response()->json([
                'status' => 301,
                'redirect' => $page->redirect_to,
                'locale' => $locale,
                'route' => ['type' => 'redirect', 'id' => $page->id],
                'seo' => null,
                'blocks' => [],
            ]);
        }

        return response()->json([
            'status' => 200,
            'redirect' => null,
            'locale' => $page->locale,
            'route' => ['type' => 'page', 'id' => $page->id],
            'seo' => $page->seo ?? ['title' => $page->title],
            'blocks' => $page->blocks ?? [],
        ]);
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');

        // Убираем хвостовой слэш, кроме корня.
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
