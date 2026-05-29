<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Filament\Resources\Pages\Pages;

use Cgehuzi\Core\Filament\Resources\Pages\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
