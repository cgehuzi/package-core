<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('locale')
                    ->label('Локаль')
                    ->options(['ru' => 'Русский', 'en' => 'English'])
                    ->default('ru')
                    ->required(),

                TextInput::make('path')
                    ->label('URL-путь')
                    ->helperText('Например: / или /about. Уникален в рамках локали.')
                    ->required(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),

                Select::make('status')
                    ->label('Статус')
                    ->options(['published' => 'Опубликовано', 'draft' => 'Черновик'])
                    ->default('published')
                    ->required(),

                TextInput::make('redirect_to')
                    ->label('Редирект на')
                    ->helperText('Если задано — резолвер вернёт редирект на этот путь.'),

                // Блочная модель контента. Builder хранит как [{type, data}].
                Builder::make('blocks')
                    ->label('Блоки контента')
                    ->blocks([
                        Block::make('hero')
                            ->label('Hero')
                            ->schema([
                                TextInput::make('heading')->label('Заголовок')->required(),
                                TextInput::make('subheading')->label('Подзаголовок'),
                            ]),
                        Block::make('text')
                            ->label('Текст')
                            ->schema([
                                Textarea::make('body')->label('Текст')->rows(4)->required(),
                            ]),
                    ])
                    ->columnSpanFull(),

                TextInput::make('seo.title')->label('SEO: заголовок'),
                Textarea::make('seo.description')->label('SEO: описание')->rows(2),
            ]);
    }
}
