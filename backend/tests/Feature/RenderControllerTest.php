<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Tests\Feature;

use Cgehuzi\Core\Models\Page;
use Cgehuzi\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RenderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_200_contract_for_published_page(): void
    {
        Page::create([
            'locale' => 'ru',
            'path' => '/',
            'title' => 'Главная',
            'status' => 'published',
            'seo' => ['title' => 'Home', 'description' => 'Demo'],
            'blocks' => [['type' => 'hero', 'data' => ['heading' => 'Hi']]],
        ]);

        $this->getJson('/api/core/render?path=/&locale=ru')
            ->assertOk()
            ->assertJson([
                'status' => 200,
                'redirect' => null,
                'locale' => 'ru',
                'route' => ['type' => 'page'],
                'seo' => ['title' => 'Home', 'description' => 'Demo'],
                'blocks' => [['type' => 'hero', 'data' => ['heading' => 'Hi']]],
            ]);
    }

    public function test_returns_404_contract_for_missing_page(): void
    {
        $this->getJson('/api/core/render?path=/nope&locale=ru')
            ->assertOk()
            ->assertJson(['status' => 404, 'route' => null, 'blocks' => []]);
    }

    public function test_returns_redirect_contract(): void
    {
        Page::create([
            'locale' => 'ru',
            'path' => '/old',
            'title' => 'Old',
            'status' => 'published',
            'redirect_to' => '/new',
        ]);

        $this->getJson('/api/core/render?path=/old&locale=ru')
            ->assertOk()
            ->assertJson(['status' => 301, 'redirect' => '/new']);
    }

    public function test_draft_page_is_not_served(): void
    {
        Page::create([
            'locale' => 'ru',
            'path' => '/draft',
            'title' => 'Draft',
            'status' => 'draft',
        ]);

        $this->getJson('/api/core/render?path=/draft&locale=ru')
            ->assertOk()
            ->assertJson(['status' => 404]);
    }
}
