<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Filament\Resources\Pages;

use BackedEnum;
use Cgehuzi\Core\Filament\Resources\Pages\Pages\CreatePage;
use Cgehuzi\Core\Filament\Resources\Pages\Pages\EditPage;
use Cgehuzi\Core\Filament\Resources\Pages\Pages\ListPages;
use Cgehuzi\Core\Filament\Resources\Pages\Schemas\PageForm;
use Cgehuzi\Core\Filament\Resources\Pages\Tables\PagesTable;
use Cgehuzi\Core\Models\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
